<?php
/**
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2014 Tritac (http://www.tritac.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Tritac_Capayable_Block_Info extends Mage_Payment_Block_Info
{

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @var Tritac_Capayable_Model_Customer
     */
    protected $_customer;

    /**
     * @var string
     */
    protected $_instructions;

    protected $invoice = NULL;


    /**
     * Set block template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('capayable/info.phtml');
    }

    public function getOrderInvoice(){
        if(is_null($this->invoice)){
            if(Mage::registry('current_order')){
                $this->invoice = Mage::registry('current_order')->getInvoiceCollection()->getFirstItem();
            }elseif($orderId = $this->getRequest()->getParam('order_id')){
                $order = Mage::getModel('sales/order')->load($orderId);
                if(!$order->getId()){
                    return FALSE;
                }
                $this->invoice = Mage::registry('current_order')->getInvoiceCollection()->getFirstItem();
            }else{
                $this->invoice = FALSE;
            }
        }
        return $this->invoice;
    }

    public function getInvoice(){
        if(is_null($this->invoice)){
            if(Mage::registry('invoice_data')) {
                $this->invoice = Mage::registry('invoice_data');
                Mage::unregister('invoice_data');
            }elseif(Mage::registry('current_invoice')){
                $this->invoice =  Mage::registry('current_invoice');
            }elseif ($invoiceId = $this->getRequest()->getParam('invoice_id')){
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
                if($invoice->getId()){
                    $this->invoice = $invoice;
                }

            }else{
                $this->invoice = FALSE;
            }
        }
        return $this->invoice;

    }

    public function setInvoice($invoice){
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * Get quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote() {
        if(!$this->_quote) {
            $this->_quote = $this->getMethod()->getInfoInstance()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Get capayable customer
     *
     * @return Tritac_Capayable_Model_Customer
     */
    public function getCustomer() {
        if(!$this->_customer) {
            $email = $this->getQuote()->getCustomerEmail();
            // If customer doesn't exists then return empty model
            $this->_customer = Mage::getModel('capayable/customer')->loadByEmail($email);
        }

        return $this->_customer;
    }

    public function getChildPdfAsArray()
    {
        $helper = Mage::helper('capayable');
        $invoice = $this->getInvoice();
        if ($invoice && $array = $helper->getInstructionsPdf(Mage::app()->getStore())) {

            foreach ($array as $n => $line) {
                $array[$n] = $helper->__($line, $invoice->getIncrementId());
            }
            return $array;
        }
    }

}