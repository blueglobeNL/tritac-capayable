<?php

class Tritac_Capayable_Model_Quote_Address_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct()
    {
        $this->setCode('capayable');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
		parent::collect($address);
	
        $address->setBaseCapayableFee(0);
        $address->setCapayableFee(0);
        $address->setCapayableFeeTaxAmount(0);
        $address->setBaseCapayableFeeTaxAmount(0);

        $paymentMethod = $address->getQuote()->getPayment()->getMethod();
        if(Mage::app()->getFrontController()->getRequest()->getParam('payment')){
            $paymentMethod = Mage::app()->getFrontController()->getRequest()->getParam('payment');
            $paymentMethod = Mage::app()->getStore()->isAdmin() && isset($paymentMethod['method']) ? $paymentMethod['method'] : null;
        }

        if (!preg_match('/^capayable/',$paymentMethod) && (!count($address->getQuote()->getPaymentsCollection()) || !$address->getQuote()->getPayment()->hasMethodInstance())){
            return $this;
        }
        
        $paymentMethod = $address->getQuote()->getPayment()->getMethodInstance();
        if(!preg_match('/^capayable/',$paymentMethod->getCode())){
            return $this;
        }

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $baseTotal = $address->getBaseGrandTotal();        

        $basePaymentsFee = $paymentMethod->getAddressCapayableFee($address);

        if (!$basePaymentsFee > 0 ) {
            return $this;
        }
        // adress is the reference for grand total
        $quote = $address->getQuote();

        $store = $quote->getStore();

        $baseTotal += $basePaymentsFee;

        $address->setBaseCapayableFee($basePaymentsFee);
        $address->setCapayableFee($store->convertPrice($basePaymentsFee,false));

        //Updating payment fee tax if it is already included into a Payment fee
        $basePaymentsTaxAmount = $paymentMethod->getCapayableTaxAmount($address);

        $address->setBaseCapayableFeeTaxAmount($basePaymentsTaxAmount);
        $address->setCapayableFeeTaxAmount($store->convertPrice($basePaymentsTaxAmount, false));
		
        // update totals
        $address->setBaseGrandTotal($baseTotal);
        $address->setGrandTotal($store->convertPrice($baseTotal, false));
		
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address) {
		
        $amount = $address->getCapayableFee();   
		$paymentTitle = Mage::helper('capayable')->getMethodTitle($address->getQuote()->getPayment()->getMethod());
        if ($amount!=0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('capayable')->__($paymentTitle),
                'value' => $amount,
            ));
        }
        return $this;
    }
}
