<?php
class Tritac_Capayable_Block_Adminhtml_Creditmemo_Totals_Fee extends Mage_Core_Block_Template
{

    public function initTotals(){
        $parent = $this->getParentBlock();
        if($parent->getSource()){
            $fee = new Varien_Object();
            $fee->setLabel($this->__('Refund Payment fee'));
            $fee->setValue($parent->getSource()->getCapayableFee());
            $fee->setBaseValue($parent->getSource()->getBaseCapayableFee());
            $fee->setCode('payment_fee');
            $fee->setBlockName('capayable_fee');
            $parent->addTotalBefore($fee,'adjustment_positive');
        }
        return $this;
    }


    public function getLabel(){
        return Mage::helper('capayable')->__('Refund %s',Mage::helper('capayable')->getMethodTitle($this->getParentBlock()->getSource()->getOrder()->getPayment()->getMethod()));
    }

    public function getIncTaxLabel(){
        //$this->getParentBlock()->getSource() == Mage_Sales_Model_Order_Creditmemo
        return Mage::helper('capayable')->__('Refund %s (Incl. Tax)',Mage::helper('capayable')->getMethodTitle($this->getParentBlock()->getSource()->getOrder()->getPayment()->getMethod()));
    }

    public function getValue(){
        return (float) Mage::app()->getStore()->roundPrice($this->getParentBlock()->getSource()->getCapayableFee());
    }

    public function getBaseValue(){
        return (float) Mage::app()->getStore()->roundPrice($this->getParentBlock()->getSource()->getBaseCapayableFee());
    }

    public function getBaseValueIncTax($format=FALSE){
        $value = $this->getParentBlock()->getSource()->getBaseCapayableFee() + $this->getParentBlock()->getSource()->getBaseCapayableFeeTaxAmount();
        if($format){
            return $this->getParentBlock()->getSource()->getOrder()->getBaseCurrency()->format($value,null,false);
        }
        return (float) Mage::app()->getStore()->roundPrice($value);
    }

}