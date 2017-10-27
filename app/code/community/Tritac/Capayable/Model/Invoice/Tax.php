<?php
class Tritac_Capayable_Model_Invoice_Tax extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $feeTax = 0;
        $baseFeeTax = 0;
        $order = $invoice->getOrder();

        $includeFeeTax = TRUE;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $prevInvoice) {
            if ($prevInvoice->getCapayableFee() && !$prevInvoice->isCanceled()) {
                $includeFeeTax = FALSE;
            }
        }

        if ($includeFeeTax) {
            $feeTax += $invoice->getOrder()->getCapayableFeeTaxAmount();
            $baseFeeTax += $invoice->getOrder()->getBaseCapayableFeeTaxAmount();
            $invoice->setCapayableFeeTaxAmount($invoice->getOrder()->getCapayableFeeTaxAmount());
            $invoice->setBaseCapayableFeeTaxAmount($invoice->getOrder()->getBaseCapayableFeeTaxAmount());
            $invoice->getOrder()->setCapayableFeeTaxAmountInvoiced($feeTax);
            $invoice->getOrder()->setBaseCapayableFeeTaxAmountInvoiced($baseFeeTax);
        }

        /**
         * Not isLast() invoice case handling
         * totalTax adjustment
         * check Mage_Sales_Model_Order_Invoice_Total_Tax::collect()
         */
        $allowedTax     = $order->getTaxAmount() - $order->getTaxInvoiced();
        $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced();
        $totalTax = $invoice->getTaxAmount();
        $baseTotalTax = $invoice->getBaseTaxAmount();
        if (!$invoice->isLast()
                && $allowedTax > $totalTax) {
            $newTotalTax           = min($allowedTax, $totalTax + $feeTax);
            $newBaseTotalTax       = min($allowedBaseTax, $baseTotalTax + $baseFeeTax);

            $invoice->setTaxAmount($newTotalTax);
            $invoice->setBaseTaxAmount($newBaseTotalTax);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalTax + $newTotalTax);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalTax + $newBaseTotalTax);
        }

        return $this;
    }
}
