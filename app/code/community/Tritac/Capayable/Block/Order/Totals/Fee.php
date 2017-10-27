<?php
class Tritac_Capayable_Block_Order_Totals_Fee extends Mage_Core_Block_Abstract
{

    protected $_order   = NULL;

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order   = $parent->getOrder();
        $payment = $this->_order->getPayment();
        $methodTitle  = Mage::helper('capayable')->getMethodTitle($payment->getMethod());
        if($this->_order->getCapayableFee()){
            $fee = new Varien_Object();
            $fee->setLabel($this->__($methodTitle));
            $fee->setValue($this->_order->getCapayableFee());
            $fee->setBaseValue($this->_order->getBaseCapayableFee());
            $fee->setCode('payment_fee');

            if (Mage::helper('capayable')->displayBothPrices()){
                $fee->setLabel($this->__('%s (Incl.Tax)',$methodTitle));

                $feeIncl = new Varien_Object();
                $feeIncl->setLabel($this->__('%s (Incl.Tax)',$methodTitle));
                $feeIncl->setValue($this->_order->getCapayableFee()+$this->_order->getCapayableFeeTaxAmount());
                $feeIncl->setBaseValue($this->_order->getBaseCapayableFee()+$this->_order->getBaseCapayableFeeTaxAmount());
                $feeIncl->setCode('payment_fee_incl');

                $parent->addTotalBefore($fee,'tax');
                $parent->addTotalBefore($feeIncl,'tax');
            }elseif(Mage::helper('capayable')->displayFeeIncludingTax()){
                $fee->setValue($this->_order->getCapayableFee()+$this->_order->getCapayableFeeTaxAmount());
                $fee->setBaseValue($this->_order->getBaseCapayableFee()+$this->_order->getBaseCapayableFeeTaxAmount());
                $parent->addTotalBefore($fee,'tax');
            }else{
                $parent->addTotalBefore($fee,'tax');
            }
        }

        return $this;
    }

}