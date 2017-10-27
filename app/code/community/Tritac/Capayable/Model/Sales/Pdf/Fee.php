<?php
class Tritac_capayable_Model_Sales_Pdf_Fee extends Mage_Sales_Model_Order_Pdf_Total_Default
{

    public function getTotalsForDisplay()
    {

        $store = $this->getOrder()->getStore();
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountInclTax = $this->getAmount()+$this->getSource()->getCapayableFeeTaxAmount();
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $method   = $this->getOrder()->getPayment()->getMethod();
        $methodTitle = Mage::helper('capayable')->getMethodTitle($method,$store);

        if (Mage::helper('capayable')->displayBothPrices()){
            $totals = array(
                array(
                    'amount'    => $this->getAmountPrefix().$amount,
                    'label'     => Mage::helper('capayable')->__('%s (Excl.Tax)',$methodTitle) . ':',
                    'font_size' => $fontSize
                ),
                array(
                    'amount'    => $this->getAmountPrefix().$amountInclTax,
                    'label'     => Mage::helper('capayable')->__('%s (Incl.Tax)',$methodTitle) . ':',
                    'font_size' => $fontSize
                ),
            );
        } elseif (Mage::helper('capayable')->displayFeeIncludingTax()) {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amountInclTax,
                'label'     => Mage::helper('capayable')->__($methodTitle) . ':',
                'font_size' => $fontSize
            ));
        } else {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amount,
                'label'     => Mage::helper('capayable')->__($methodTitle) . ':',
                'font_size' => $fontSize
            ));
        }

        return $totals;
    }
}
