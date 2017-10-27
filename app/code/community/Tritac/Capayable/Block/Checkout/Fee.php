<?php
class Tritac_Capayable_Block_Checkout_Fee extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'capayable/checkout/fee.phtml';

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return Mage::helper('capayable')->displayBothPrices();
    }

    /**
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
    public function getPaymentsFeeIncludeTax()
    {
        $paymentFeeInclTax = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $paymentFeeInclTax += $address->getCapayableFee() + $address->getCapayableFeeTaxAmount();
        }
        return $paymentFeeInclTax;
    }

    /**
     * Get Payment fee exclude tax
     *
     * @return float
     */
    public function getPaymentsFeeExcludeTax()
    {
        $paymentFeeExclTax = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $paymentFeeExclTax += $address->getCapayableFee();
        }
        return $paymentFeeExclTax;
    }

    /**
     * Get label for Payment fee include tax
     *
     * @return float
     */
    public function getIncludeTaxLabel()
    {
        return $this->helper('capayable')->__('Payment fee (Incl.Tax)');
    }

    /**
     * Get label for Payment fee exclude tax
     *
     * @return float
     */
    public function getExcludeTaxLabel()
    {
        return $this->helper('capayable')->__('Payment fee (Excl.Tax)');
    }
}
