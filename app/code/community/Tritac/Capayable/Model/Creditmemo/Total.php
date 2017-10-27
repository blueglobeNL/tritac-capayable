<?php
class Tritac_Capayable_Model_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $cm)
    {

        $order = $cm->getOrder();

        if (!preg_match('/capayable/',$order->getPayment()->getMethodInstance()->getCode())){
            return $this;
        }
        $baseCmTotal = $cm->getBaseGrandTotal();
        $cmTotal = $cm->getGrandTotal();
		$baseTaxAmount = $cm->getBaseTaxAmount();
		$taxAmount = $cm->getTaxAmount();

        $basePaymentFeeInvoiced		= $order->getBaseCapayableFeeInvoiced();
        $paymentFeeInvoiced			= $order->getCapayableFeeInvoiced();
		$basePaymentFeeTaxAmount	= $order->getBaseCapayableFeeTaxAmount();
		$paymentFeeTaxAmount		= $order->getCapayableFeeTaxAmount();

        if ($cm->getInvoice()){
            $invoice = $cm->getInvoice();
            $basePaymentsFeeToCredit = $invoice->getBaseCapayableFee();
            $paymentFeeToCredit = $invoice->getCapayableFee();
			$basePaymentFeeTaxAmountToCredit = $invoice->getBaseCapayableFeeTaxAmount();
			$paymentFeeTaxAmountToCredit = $invoice->getCapayableFeeTaxAmount();
        }else{
            $basePaymentsFeeToCredit = $basePaymentFeeInvoiced;
            $paymentFeeToCredit = $paymentFeeInvoiced;
			$basePaymentFeeTaxAmountToCredit = $basePaymentFeeTaxAmount;
			$paymentFeeTaxAmountToCredit = $paymentFeeTaxAmount;
        }

        if (!$basePaymentsFeeToCredit > 0){
            return $this;
        }
        // Subtracting invoiced Payment fee from Credit memo total
		
        $cm->setBaseGrandTotal($baseCmTotal+($basePaymentsFeeToCredit + $basePaymentFeeTaxAmountToCredit));
        $cm->setGrandTotal($cmTotal+($paymentFeeToCredit + $paymentFeeTaxAmountToCredit));
		$cm->setTaxAmount($taxAmount + $paymentFeeTaxAmountToCredit);
		$cm->setBaseTaxAmount($baseTaxAmount + $basePaymentFeeTaxAmountToCredit);
        $cm->setBaseCapayableFee($basePaymentsFeeToCredit);
        $cm->setCapayableFee($paymentFeeToCredit);
		$cm->setBaseCapayableFeeTaxAmount($basePaymentFeeTaxAmountToCredit);
		$cm->setCapayableFeeTaxAmount($paymentFeeTaxAmountToCredit);

        return $this;
    }
}