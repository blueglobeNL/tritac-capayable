<?php
/**
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2014 Tritac (http://www.tritac.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Tritac_Capayable_Helper_Data extends Mage_Core_Helper_Abstract {

    const CONFIG_XML_PATH_PAYMENT_METHOD_COST_PATH						= 'payment/%s/cost';
    const CONFIG_XML_PATH_PAYMENT_METHOD_FOREIGN_COST_PATH				= 'payment/%s/foreigncost';
    const CONFIG_XML_PATH_PAYMENT_METHOD_TITLE							= 'payment/%s/title';
    const CONFIG_XML_PATH_PAYMENT_ORDER_STATUS				            = 'payment/%s/order_status';
    const CONFIG_XML_PATH_PAYMENT_INSTRUCTIONS							= 'payment/capayable/instructions';
    const CONFIG_XML_PATH_PAYMENT_INSTRUCTIONS_PDF						= 'payment/capayable/instructions_pdf';
    const CONFIG_XML_PATH_PAYMENT_BYPASS_EXT_VAL     			        = 'payment/capayable/bypass_ext_val';
    const CONFIG_XML_PATH_CAPAYABLE_FEE_TAX_CLASS						= 'tax/capayable/capayable_fee_tax_class';
    const CONFIG_XML_PATH_CAPAYABLE_FEE_INCLUDES_TAX					= 'tax/capayable/capayable_fee_includes_tax';
    const CONFIG_XML_PATH_CAPAYABLE_DISPLAY_PAYMENTS_FEE     			= 'tax/capayable/display_capayable_fee';


    public function getMethodTitle($method,$store=NULL){
        return Mage::getStoreConfig(sprintf(self::CONFIG_XML_PATH_PAYMENT_METHOD_TITLE,$method),$store);
    }

    public function getPaymentsFeeTaxClass($store=NULL){
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CAPAYABLE_FEE_TAX_CLASS,$store);
    }

    public function capayableFeePriceIncludesTax($store=NULL){
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CAPAYABLE_FEE_INCLUDES_TAX,$store);
    }

    public function getOrderStatus($store=null){
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_PAYMENT_ORDER_STATUS,$store);
    }

    public function getInstructions($store=null){
        return preg_split('/\n/',Mage::getStoreConfig(self::CONFIG_XML_PATH_PAYMENT_INSTRUCTIONS,$store),-1,PREG_SPLIT_NO_EMPTY);
    }

    public function getInstructionsPdf($store=null){
        return preg_split('/\n/',Mage::getStoreConfig(self::CONFIG_XML_PATH_PAYMENT_INSTRUCTIONS,$store),-1,PREG_SPLIT_NO_EMPTY);
    }

    public function displayBothPrices($store=NULL){
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CAPAYABLE_DISPLAY_PAYMENTS_FEE,$store) == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    public function displayFeeIncludingTax($store=NULL)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CAPAYABLE_DISPLAY_PAYMENTS_FEE,$store) == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayFeeExcludingTax($store=NULL)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CAPAYABLE_DISPLAY_PAYMENTS_FEE,$store) == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }


    public function getPaymentMethodCost($method,Mage_Customer_Model_Address_Abstract $address=NULL){
        $path = ($this->isForeignShipping($address))?self::CONFIG_XML_PATH_PAYMENT_METHOD_FOREIGN_COST_PATH:self::CONFIG_XML_PATH_PAYMENT_METHOD_COST_PATH;
        return Mage::getStoreConfig(sprintf($path,$method));
    }


    public function isForeignShipping(Mage_Customer_Model_Address_Abstract $address=NULL){
        $shippingCountry = (!is_null($address))?$address->getcountry():Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountry();
        $storeCountry    =  Mage::getStoreConfig('general/country/default');
        return ($shippingCountry != $storeCountry);
    }


    public function getCapayableFeePrice($price, $incTax = true, $shippingAddress = NULL, $customerTaxClass = NULL, $store = NULL){
        $taxPriceRequest = new Varien_Object();
        $taxPriceRequest->setTaxClassId($this->getPaymentsFeeTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }
        return Mage::helper('tax')->getPrice(
            $taxPriceRequest,
            $price,
            $incTax,
            $shippingAddress,
            $billingAddress,
            $customerTaxClass,
            $store,
            $this->capayableFeePriceIncludesTax($store)
        );
    }


    /**
     * Get public key
     *
     * @return string
     */
    public function getPublicKey() {
        $public_key = Mage::getStoreConfig('payment/capayable/public_key');
        return $public_key;
    }

    /**
     * Get secret key
     *
     * @return string
     */
    public function getSecretKey() {
        $secret_key = Mage::getStoreConfig('payment/capayable/secret_key');
        return $secret_key;
    }

    /**
     * Get current extension environment
     *
     * @return string
     */
    public function getMode($store=null) {
        $configMode = Mage::getStoreConfigFlag('payment/capayable/test',$store);
        if($configMode) {
            return 'test';
        } else {
            return 'production';
        }
    }

    public function bypassExtValidation($store=null){
        if(!Mage::getStoreConfigFlag('payment/capayable/test',$store)){
            return false;
        }if(!Mage::getStoreConfigFlag(self::CONFIG_XML_PATH_PAYMENT_BYPASS_EXT_VAL,$store)){
            return FALSE;
        }
        if(Mage::helper('core')->isDevAllowed($store)){
            return TRUE;
        }
        return false;
    }

    /**
     * Convert price to cents.
     *
     * @param $amount
     * @return int
     */
    public function convertToCents($amount) {

        return ($amount * 100);
    }
}