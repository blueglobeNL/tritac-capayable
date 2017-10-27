<?php
class Tritac_Capayable_Block_Invoice_Totals_Fee extends Mage_Core_Block_Abstract
{

    protected $_invoice = NULL;

    public function initTotals()
    {
        // $parent 			= Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
        // $this->_invoice	= Mage_Sales_Model_Order_Invoice
        $parent = $this->getParentBlock();
        $this->_invoice   = $parent->getInvoice();
        if(!$this->_invoice){
            return $this;
        }
        $payment = $this->_invoice->getOrder()->getPayment();
        $methodTitle  = Mage::helper('capayable')->getMethodTitle($payment->getMethod());
        if($this->_invoice->getCapayableFee()){
            $fee = new Varien_Object();
            $fee->setLabel($this->__($methodTitle));
            $fee->setValue($this->_invoice->getCapayableFee());
            $fee->setBaseValue($this->_invoice->getBaseCapayableFee());
            $fee->setCode('payment_fee');
            if (Mage::helper('capayable')->displayBothPrices()){
                $fee->setLabel($this->__('Payment fee (Excl.Tax)'));

                $feeIncl = new Varien_Object();
                $feeIncl->setLabel($this->__('Payment fee (Incl.Tax)'));
                $feeIncl->setValue($this->_invoice->getCapayableFee()+$this->_invoice->getCapayableFeeTaxAmount());
                $feeIncl->setBaseValue($this->_invoice->getBaseCapayableFee()+$this->_invoice->getBaseCapayableFeeTaxAmount());
                $feeIncl->setCode('payment_fee_incl');

                $parent->addTotalBefore($fee,'tax');
                $parent->addTotalBefore($feeIncl,'tax');
            }elseif(Mage::helper('capayable')->displayFeeIncludingTax()){
                $fee->setValue($this->_invoice->getCapayableFee()+$this->_invoice->getCapayableFeeTaxAmount());
                $fee->setBaseValue($this->_invoice->getBaseCapayableFee()+$this->_invoice->getBaseCapayableFeeTaxAmount());
                $parent->addTotalBefore($fee,'tax');
            }else{
                $parent->addTotalBefore($fee,'tax');
            }
        }
        return $this;
    }

}