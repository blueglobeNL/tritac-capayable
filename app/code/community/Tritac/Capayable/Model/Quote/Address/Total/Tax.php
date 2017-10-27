<?php

class Tritac_Capayable_Model_Quote_Address_Total_Tax extends Mage_Sales_Model_Quote_Address_Total_Tax {

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {

        $paymentMethod = Mage::app()->getFrontController()->getRequest()->getParam('payment');
        $paymentMethod = Mage::app()->getStore()->isAdmin() && isset($paymentMethod['method']) ? $paymentMethod['method'] : null;
        if (!preg_match('/^capayable/',$paymentMethod) && (!count($address->getQuote()->getPaymentsCollection()) || !$address->getQuote()->getPayment()->hasMethodInstance())){            
            return $this;
        }

        $paymentMethod = $address->getQuote()->getPayment()->getMethodInstance();
		if(!preg_match('/^capayable/',$paymentMethod->getCode())){
            return $this;
        }

        $store = $address->getQuote()->getStore();        

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();

        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        /* @var $taxCalculationModel Mage_Tax_Model_Calculation */
        $request = $taxCalculationModel->getRateRequest($address, $address->getQuote()->getBillingAddress(), $custTaxClassId, $store);
        $paymentsFeeTaxClass = Mage::helper('capayable')->getPaymentsFeeTaxClass($store);
		
        $paymentsFeeTax      = 0;
        $paymentsFeeBaseTax  = 0;

        if ($paymentsFeeTaxClass) {
            if ($rate = $taxCalculationModel->getRate($request->setProductClassId($paymentsFeeTaxClass))) {

                if (!Mage::helper('capayable')->capayableFeePriceIncludesTax()) {
                    $paymentsFeeTax    = $address->getCapayableFee() * $rate/100;
                    $paymentsFeeBaseTax= $address->getBaseCapayableFee() * $rate/100;
                } else {
                    $paymentsFeeTax    = $address->getCapayableFeeTaxAmount();
                    $paymentsFeeBaseTax= $address->getBaseCapayableFeeTaxAmount();
                }

                $paymentsFeeTax    = $store->roundPrice($paymentsFeeTax);
                $paymentsFeeBaseTax= $store->roundPrice($paymentsFeeBaseTax);

                $address->setTaxAmount($address->getTaxAmount() + $paymentsFeeTax);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $paymentsFeeBaseTax);

                $this->_saveAppliedTaxes(
                    $address,
                    $taxCalculationModel->getAppliedRates($request),
                    $paymentsFeeTax,
                    $paymentsFeeBaseTax,
                    $rate
                );
            }
        }

		$address->setCapayableFeeTaxAmount($paymentsFeeTax);
        $address->setBaseCapayableFeeTaxAmount($paymentsFeeBaseTax);

        $address->setGrandTotal($address->getGrandTotal() + $address->getCapayableFeeTaxAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseCapayableFeeTaxAmount());

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {        
        $store = $address->getQuote()->getStore();
        /**
         * Modify subtotal
         */
        if (Mage::getSingleton('tax/config')->displayCartSubtotalBoth($store) || Mage::getSingleton('tax/config')->displayCartSubtotalInclTax($store)) {
            if ($address->getSubtotalInclTax() > 0) {
                $subtotalInclTax = $address->getSubtotalInclTax();
            } else {
                $subtotalInclTax = $address->getSubtotal()+$address->getTaxAmount()-$address->getShippingTaxAmount()-$address->getCapayableFeeTaxAmount();
            }            

            $address->addTotal(array(
                'code'      => 'subtotal',
                'title'     => Mage::helper('sales')->__('Subtotal'),
                'value'     => $subtotalInclTax,
                'value_incl_tax' => $subtotalInclTax,
                'value_excl_tax' => $address->getSubtotal(),
            ));
        }
        return $this;
    }
}
