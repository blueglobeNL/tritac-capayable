<?php
class Tritac_Capayable_Block_Adminhtml_Sales_Order_Create_Totals_Fee extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    protected $_template = 'capayable/sales/order/create/totals/fee.phtml';

    /**
     * Check if we need display Payment fee include and exlude tax
     *
     * @return bool
     */
    public function displayBoth()
    {
        return Mage::helper('capayable')->displayBothPrices();
    }

    /**
     * Check if we need display Payment fee include tax
     *
     * @return bool
     */
    public function displayIncludeTax()
    {
        return Mage::helper('capayable')->displayFeeIncludingTax();
    }

    /**
     * Get Payment fee include tax
     *
     * @return float
     */
    public function getPaymentsFeeIncludeTax(){
        return (float)$this->getTotal()->getAddress()->getCapayableFee() + (float)$this->getTotal()->getAddress()->getCapayableFeeTaxAmount();
    }

    /**
     * Get Payment fee exclude tax
     *
     * @return float
     */
    public function getPaymentsFeeExcludeTax(){
        return $this->getTotal()->getAddress()->getCapayableFee();
    }

    /**
     * Get label for Payment fee include tax
     *
     * @return string
     */
    public function getIncludeTaxLabel(){
        return Mage::helper('capayable')->__('%s Incl. Tax',Mage::helper('capayable')->getMethodTitle($this->getQuote()->getPayment()->getMethod()));
    }

    /**
     * Get label for Payment fee exclude tax
     *
     * @return string
     */
    public function getExcludeTaxLabel(){
        return Mage::helper('capayable')->__('%s Excl. Tax',Mage::helper('capayable')->getMethodTitle($this->getQuote()->getPayment()->getMethod()));
    }
}
