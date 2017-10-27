<?php

class Tritac_Capayable_Model_Invoice_Total extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        if (!preg_match('/^capayable/',$order->getPayment()->getMethodInstance()->getCode())){
            return $this;
        }

        if (!$order->getCapayableFee()){
            return $this;
        }

        foreach ($invoice->getOrder()->getInvoiceCollection() as $prevInvoice) {
            if ($prevInvoice->getCapayableFeeAmount() && !$prevInvoice->isCanceled()) {
                $includeFeeTax = FALSE;
            }
        }

        $basePaymentFee = $order->getBaseCapayableFee();
        $basePaymentFeeInvoiced = $order->getBaseCapayableFeeInvoiced();
        $baseInvoiceTotal = $invoice->getBaseGrandTotal();
        $paymentFee = $order->getCapayableFee();
        $paymentFeeInvoiced = $order->getCapayableFeeInvoiced();
        $invoiceTotal = $invoice->getGrandTotal();

        if (!$basePaymentFee || $basePaymentFeeInvoiced==$basePaymentFee) {
            return $this;
        }

        $basePaymentFeeToInvoice = $basePaymentFee - $basePaymentFeeInvoiced;
        $paymentFeeToInvoice = $paymentFee - $paymentFeeInvoiced;

        $baseInvoiceTotal = $baseInvoiceTotal + $basePaymentFeeToInvoice;
        $invoiceTotal = $invoiceTotal + $paymentFeeToInvoice;

        $invoice->setBaseGrandTotal($baseInvoiceTotal);
        $invoice->setGrandTotal($invoiceTotal);

        $invoice->setBaseCapayableFee($basePaymentFeeToInvoice);
        $invoice->setCapayableFee($paymentFeeToInvoice);

        $order->setBaseCapayableFeeInvoiced($basePaymentFeeInvoiced+$basePaymentFeeToInvoice);
        $order->setCapayableFeeInvoiced($paymentFeeInvoiced+$paymentFeeToInvoice);

        return $this;
    }
}
