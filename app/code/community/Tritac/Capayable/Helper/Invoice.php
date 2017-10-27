<?php
/**
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2014 Tritac (http://www.tritac.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Tritac_Capayable_Helper_Invoice extends Tritac_Capayable_Helper_Data
{

	/*
	* Create Magento invoice from order and send Bcc emails to capayable
	*
	* @param	Mage_Sales_Model_Order order
	* @return	string	Invoice Increment ID
	*/
    public function create(Mage_Core_Model_Order $order)
    {

        /** @var $payment */
        $payment = $order->getPayment();

        /** @var $paymentInstance */
        $paymentInstance = $payment->getMethodInstance();

        // Return if another payment method was used
        if($paymentInstance->getCode() != 'capayable'){
			return FALSE;
		}

        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, true);
        $customerEmail = $order->getCustomerEmail();
        $_capayableCustomer = Mage::getModel('capayable/customer')->loadByEmail($customerEmail);

        /**
         * Customer credit check
         */
        $amount = $this->convertToCents($order->getGrandTotal());

        // make sure you can invoice!
        if (!$order->canInvoice()) {
            return $this;
        }

        try {
            // Initialize new magento invoice
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            // Set magento transaction id which returned from capayable
            $invoice->setTransactionId($payment->getLastTransId());

            // Allow payment capture and register new magento transaction
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

            // Register invoice and apply it to order, order items etc.
            $invoice->register();
            $invoice->setEmailSent(true);
            $invoice->getOrder()->setIsInProcess(true);

            $transaction = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            // Commit changes or rollback if error has occurred
            $transaction->save();

            /**
             * Register invoice with Capayable
             */

            $isApiInvoiceAccepted = $paymentInstance->processApiInvoice($invoice);

            if($isApiInvoiceAccepted) {
    			$coreConfig = new Mage_Core_Model_Config();
				/*
				* @TODO: Fix configurations settings so that stores don't need to reinitialies
				*/
    			$old_copy_to = Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO);
    			$old_copy_method = Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD);
    			$coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO, "capayable-invoice-bcc@tritac.com");
    			$coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD, "bcc");

    			Mage::getConfig()->reinit();
    			Mage::app()->reinitStores();

    			Mage::register('invoice_data', $invoice_data);

    			// Send email notification about registered invoice
    			$invoice->sendEmail(true);


    			$coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO, "{$old_copy_to}");
    			$coreConfig->saveConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD, "{$old_copy_method}");

    			Mage::getConfig()->reinit();
    			Mage::app()->reinitStores();
				
				// All done, return new Invoice increment-ID
				return $invoice->getIncrementId();
            } else {
    			$this->_getSession()->addError($this->__('Failed to send the invoice.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
        }
    }
}