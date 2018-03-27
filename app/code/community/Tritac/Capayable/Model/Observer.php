<?php
/**
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2014 Tritac (http://www.tritac.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Tritac_Capayable_Model_Observer
{
    protected $_logging = true;
    protected $_logfile;

    public function __construct()
    {
        $date               = date('Y-m-d');
        $this->_logfile     = 'capayable_'.$date.'.log';
    }

    /**
     * processAfterShipment
     *      - called in the event of a shipment (see config.xml)
     *      - in case of postpayment, send an invoice only if all items are shipped
     *      - in case of payinterms, prevent order going to complete
     *
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function processAfterShipment(Varien_Event_Observer $observer)
    {
        $event      = $observer->getEvent();
        $shipment   = $event->getShipment();
        $order      = $shipment->getOrder();
        $payment    = $order->getPayment();
        $paymentInstance = $payment->getMethodInstance();

        // for Postpayment make an invoice
        if($paymentInstance->getCode() == 'capayable_postpayment') {

            // Return if there are still items to be shipped
            foreach ($order->getAllItems() as $item) {
                if ($item->getQtyToShip() > 0 && !$item->getLockedDoShip()) {
                    return false;
                }
            }
            $this->sendInvoice($order);
        }
    }

    /**
     * sendInvoice
     *      - called by both processAfterShipment and processOrderInvoice
     *
     * @param Mage_Sales_Model_Order $order
     */
    public function sendInvoice(Mage_Sales_Model_Order $order)
    {
        if($this->_logging){
            Mage::log('in sendInvoice', null, $this->_logfile);
        }
        $payment            = $order->getPayment();
        $paymentInstance    = $payment->getMethodInstance();
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, true);
        // check if it is possible to invoice!
        if ($order->canInvoice()){
            if($this->_logging){
                Mage::log('sendInvoice sending invoice for '.$paymentInstance->getCode(), null, $this->_logfile);
            }
            try {
                // Initialize new magento invoice
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                // Set magento transaction id which returned from capayable
                $invoice->setTransactionId($payment->getLastTransId());
                // Allow payment capture and register new magento transaction
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);

                // TODO: get following to work
                //$invoice->addComment('U betaald via Achteraf betalen.', true, true);
                //$invoice->getOrder()->setCustomerNoteNotify(true);

                // Register invoice and apply it to order, order items etc.
                $invoice->register();

                $transaction = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());

                // Commit changes or rollback if error has occurred
                $transaction->save();

                /**
                 * Register invoice with Capayable
                 */
                $isApiInvoiceAccepted = $paymentInstance->processApiInvoice($invoice);
                if($this->_logging) {
                    Mage::log('sendInvoice isApiInvoiceAccepted:', null, $this->_logfile);
                    Mage::log($isApiInvoiceAccepted, null, $this->_logfile);
                }
                //if ($isApiInvoiceAccepted) {
                    $invoice->getOrder()->setIsInProcess(true);
                    $invoice->getOrder()->addStatusHistoryComment(
                        'Invoice created and email send', true
                    );
                    $invoice->sendEmail(true, '');
                    $order->save();
//                } else {
//                    $this->_getSession()->addError(Mage::helper('capayable')->__('Failed to send the invoice.'));
//                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
        } else {
            Mage::log('sendInvoice: could not send invoice, invoice already sent.', null, $this->_logfile);
        }
    }


    public function processInvoiceCapayable(Varien_Event_Observer $observer)
    {

        $event      = $observer->getEvent();
        $invoice    = $event->getInvoice();
        $order      = $invoice->getOrder();
        $payment    = $order->getPayment();
        $paymentInstance = $payment->getMethodInstance();

        // Return if another payment method was used
        if($paymentInstance->getCode() != 'capayable') {
            return false;
        }

        try {

            $isApiInvoiceAccepted = $paymentInstance->processApiInvoice($invoice);

            if ($isApiInvoiceAccepted) {

                $coreConfig         = new Mage_Core_Model_Config();
                $old_copy_to        = Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO);
                $old_copy_method    = Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD);
                $coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO, "capayable-invoice-bcc@tritac.com");
                $coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD, "bcc");

                Mage::getConfig()->reinit();
                Mage::app()->reinitStores();

                Mage::register('invoice_data', $invoice);

                // Send email notification about registered invoice
                $invoice->sendEmail(true);

                $coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO, "{$old_copy_to}");
                $coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD, "{$old_copy_method}");

                Mage::getConfig()->reinit();
                Mage::app()->reinitStores();

            } else {
                $this->_getSession()->addError(Mage::helper('capayable')->__('Failed to send the invoice.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
        }
    }


    /**
     * If order paid with capayable Postpayment payment method then disable creating order invoice.
     * Invoice will be created automatically only after creating shipment.
     *
     * @param Varien_Event_Observer $observer
     */
    public function processOrderInvoice(Varien_Event_Observer $observer) {
        $event      = $observer->getEvent();
        $_order     = $event->getOrder();
        $_payment   = $_order->getPayment();
        if($this->_logging){
            Mage::log('processOrderInvoice payment_method : '.$_payment->getMethod(), null, $this->_logfile);
        }
        /**
         * Set order invoice flag to false
         *
         * @see Mage_Sales_Model_Order::canInvoice();
         * @see Mage_Adminhtml_Block_Sales_Order_View::__construct(); Do not add invoice button to adminhtml view.
         */
        if(is_object($_payment) && ($_payment->getMethod() == 'capayable_postpayment')) {
            $_order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, false);
        }
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }


    public function sales_quote_collect_totals_after(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        $quote->setCapayableFee(0);
        $quote->setBaseCapayableFee(0);
        $quote->setCapayableFeeTaxAmount(0);
        $quote->setBaseCapayableFeeTaxAmount(0);
        // why did they make this a float in the past? it is very dangerous to do math with floats in PHP...
        foreach ($quote->getAllAddresses() as $address) {
            // why add something that you set to zero???? I really don't understand...
            if($this->_logging) {
                Mage::log('sl_ord_crdm_sv_bfr adrs getCapFee : ' . $address->getCapayableFee(), null, $this->_logfile);
                Mage::log('sl_ord_crdm_sv_bfr adrs getBaseCapFee : ' . $address->getBaseCapayableFee(), null, $this->_logfile);
            }
            $quote->setCapayableFee((float) $quote->getCapayableFee()+$address->getCapayableFee());
            $quote->setBaseCapayableFee((float) $quote->getBaseCapayableFee()+$address->getBaseCapayableFee());

            $quote->setCapayableFeeTaxAmount((float) $quote->getCapayableFeeTaxAmount()+$address->getCapayableFeeTaxAmount());
            $quote->setBaseCapayableFeeTaxAmount((float) $quote->getBaseCapayableFeeTaxAmount()+$address->getBaseCapayableFeeTaxAmount());
        }
    }

    public function sales_order_creditmemo_save_before(Varien_Event_Observer $observer){
        if($this->_logging) {
            Mage::log('sales_order_creditmemo_save_before ', null, $this->_logfile);
        }
        $post = Mage::app()->getFrontController()->getAction()->getRequest()->getPost('creditmemo');
        if($this->_logging) {
            Mage::log($post, null, $this->_logfile);
        }

        // If the order was not payed with capayable, the field 'capayable_amount' will not be available in the creditmemo totals form, so we skip it.

        // very funny :-( it appears that if you make a credit memo and you send a copy to the customer with a comment,
        // the credit memo post will not always(!) contain the capayable_amount!
        //if(!isset($post['capayable_amount'])){
         //   return NULL;
        //}

        $cm         = $observer->getCreditmemo();
        $order      = $cm->getOrder();
        $payment    = $order->getPayment();
        $paymntInst = $payment->getMethodInstance();
        $paymntCode = $paymntInst->getCode();  // is capayable_postpayment of capayable_payinterms
        if(substr($paymntCode, 0, 9) != 'capayable'){
            return NULL;
        }

        if(!isset($post['capayable_amount'])){
            if($this->_logging) {
                Mage::log('no capayable_amount in post', null, $this->_logfile);
            }
            // okay, then set it to 0
            $baseCreditCapayableAmount = 0;
        } else {
            $baseCreditCapayableAmount = (float)$post['capayable_amount'];
            if ($this->_logging) {
                Mage::log('baseCreditCapayableAmount : ' . $baseCreditCapayableAmount, null, $this->_logfile);
            }
        }

        $maxAllowed     = ($order->getBaseCapayableFee() + $order->getBaseCapayableFeeTaxAmount()) -
            ($order->getBaseCapayableFeeRefunded() + $order->getBaseCapayableFeeTaxAmountRefunded()); // 5.95
        if($this->_logging) {
            Mage::log('maxallowed : ' . $maxAllowed, null, $this->_logfile);
        }
        // The creditmemo numbers are allready modified by Tritac_Capayable_Model_Creditmemo_Total
        // The payment fee from the order is already added to the GrandTotal in that Model.
        // That has to be correct according to the submitted payment-fee refund.

        $orgBaseCapFee      = $cm->getBaseCapayableFee();
        $orgBaseCapFeeTaxAm = $cm->getBaseCapayableFeeTaxAmount();
        if($this->_logging){
            Mage::log('orgBaseCapFee :'.$orgBaseCapFee, null, $this->_logfile);
            Mage::log('orgBaseCapFeeTaxAm :'.$orgBaseCapFeeTaxAm, null, $this->_logfile);
        }

        // $baseTotalCorrection must be applied on creditmemo.grand_total and creditmemo.base_grand_total
        $baseTotalCorrection = $baseCreditCapayableAmount - ($orgBaseCapFee + $orgBaseCapFeeTaxAm);
        // to avoid "Warning: Division by zero in ..." errors, check first:
        if($orgBaseCapFee > 0 || $orgBaseCapFeeTaxAm > 0) {
            $factor = $baseCreditCapayableAmount / ($orgBaseCapFee + $orgBaseCapFeeTaxAm);
        } else {
            $factor = 0;
        }
        $baseRefund	        = $factor * $orgBaseCapFee;
        $baseTaxRefund	    = $factor * $orgBaseCapFeeTaxAm;
        $baseTaxCorrection	= $baseTaxRefund - $orgBaseCapFeeTaxAm;
        if ($this->_logging) {
            Mage::log('baseRefund : '.$baseRefund, null, $this->_logfile);
            Mage::log('baseTaxRefund : '.$baseTaxRefund, null, $this->_logfile);
            Mage::log('baseTaxCorrection : '.$baseTaxCorrection, null, $this->_logfile);
        }

        if($baseCreditCapayableAmount > $maxAllowed) {
            if ($this->_logging) {
                Mage::log('baseCreditCapAm (' . $baseCreditCapayableAmount.') > maxAllowed (' . $maxAllowed . ')', null, $this->_logfile);
                Mage::log('Maximum Payment Fee amount allowed to refund is: (Incl. Tax)' . $maxAllowed, null, $this->_logfile);
            }
            Mage::throwException(Mage::helper('capayable')->__('Maximum Payment Fee amount allowed to refund is: %s (Incl. Tax)',
                ($order->getBaseCurrency()->format($maxAllowed,null,false)))
            );
        }

        $cm->setGrandTotal($cm->getGrandTotal() + $baseTotalCorrection);
        $cm->setBaseGrandTotal($cm->getBaseGrandTotal() + $baseTotalCorrection);
        $cm->setBaseTaxAmount($cm->getBaseTaxAmount() + $baseTaxCorrection);
        $cm->setTaxAmount($cm->getTaxAmount() + $baseTaxCorrection);
        $order->setBaseTaxRefunded($cm->getBaseTaxAmount());
        $order->setTaxRefunded($cm->getTaxAmount());
        $order->setBaseTotalRefunded($order->getBaseTotalOfflineRefunded() + $order->getBaseTotalOnlineRefunded());
        $order->setTotalRefunded($order->getTotalOfflineRefunded() + $order->getTotalOnlineRefunded());
        $cm->setBaseCapayableFee($baseRefund);
        $cm->setCapayableFee($baseRefund);
        $cm->setBaseCapayableFeeTaxAmount($baseTaxRefund);
        $cm->setCapayableFeeTaxAmount($baseTaxRefund);
        $order->setBaseCapayableFeeRefunded($baseRefund);
        $order->setCapayableFeeRefunded($baseRefund);
        $order->setBaseCapayableFeeTaxAmountRefunded($baseTaxRefund);
        $order->setCapayableFeeTaxAmountRefunded($baseTaxRefund);

        $baseTotalOfflineRefunded = $order->getBaseTotalOfflineRefunded();
        if ($this->_logging) {
            Mage::log('base total offline refunded ' . $baseTotalOfflineRefunded, null, $this->_logfile);
        }

        $paymntInst->refund($payment, $baseTotalOfflineRefunded);

        if ($this->_logging) {
            Mage::log('sales_order_creditmemo_save_before done ', null, $this->_logfile);
        }
    }

    /**
     * Adds Payment Fee to order
     * @param Varien_Event_Observer $observer
     */

    public function sales_order_payment_place_end(Varien_Event_Observer $observer) {
        $payment = $observer->getPayment();
        if (!preg_match('/^capayable/',$payment->getMethodInstance()->getCode())){
            return;
        }

        $order = $payment->getOrder();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (! $quote->getId()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }
        $order->setCapayableFee($quote->getCapayableFee());
        $order->setBaseCapayableFee($quote->getBaseCapayableFee());

        $order->setCapayableFeeTaxAmount($quote->getCapayableFeeTaxAmount());
        $order->setBaseCapayableFeeTaxAmount($quote->getBaseCapayableFeeTaxAmount());

        $order->save();
    }
}