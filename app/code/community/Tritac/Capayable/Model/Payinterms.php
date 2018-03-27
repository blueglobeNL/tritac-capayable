<?php
/**
 * Capayable payment method model
 *
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2016 Tritac (http://www.tritac.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      (last author) Isolde van Oosterhout
 */

class Tritac_Capayable_Model_Payinterms extends Mage_Payment_Model_Method_Abstract
{
    const TEST_URL  = 'https://capayable-api-test.tritac.com';
    const PROD_URL 	= 'https://capayable-api.tritac.com';

    const MALE      = 'MALE';
    const FEMALE    = 'FEMALE';
    const UNKNOWN   = 'UNKNOWN';

    /**
     * Unique internal payment method identifier
     */
    protected $_code                = 'capayable_payinterms';
    protected $_paymentMethod       = 'Capayable Pay in terms';
    protected $_formBlockType       = 'capayable/form';
    protected $_infoBlockType       = 'capayable/info';
    protected $_logging             = true;
    protected $_logfile;
    protected $_url                 = '';

    /**
     * Availability options
     */
    protected $_isGateway           = true;
    protected $_canOrder            = true;
    protected $_canAuthorize        = true;
    protected $_canCapture          = true;
    protected $_canCapturePartial   = true;
    protected $_canRefund           = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid             = false;

    public function __construct()
    {
        $date               = date('Y-m-d');
        $this->_logfile     = 'capayable_'.$date.'.log';
        if(Mage::helper('capayable')->getMode() == null || Mage::helper('capayable')->getMode() == 'production') {
            $this->_url     = self::PROD_URL;
        } else {
            $this->_url     = self::TEST_URL;
        }

        parent::__construct();
    }

    /**
     * Check customer credit via capayable
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Mage_Payment_Model_Info_Exception
     */
    public function authorize(Varien_Object $payment, $amount) {

        if($this->_logging) {
            Mage::log('Payinterms - authorize', null, $this->_logfile);
        }
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('capayable')->__('The amount due must be greater than 0.'));
        }

        // Convert amount to cents
        $amount     = Mage::helper('capayable')->convertToCents($amount);
        $order      = $payment->getOrder();

        // Load saved capayable customer if exists. Otherwise load empty model.
        $capayableCustomer = Mage::getModel('capayable/customer')->loadByEmail($order->getCustomerEmail());

        // Throw exception if capayable can't provide customer credit
        $result = $this->checkCredit($capayableCustomer, $amount, true);
        if(!$result->getIsAccepted()) {
            throw new Mage_Payment_Model_Info_Exception(
                Mage::helper('capayable')->__('The payment was refused by Capayable') . ": " .
                Mage::helper('capayable')->__($result->getRefuseReason()) . " " .
                Mage::helper('capayable')->__('For additional information contact Capayable on +31 40 - 259 5072.')
            );
        }

        if(!$result->getFirstInstallmentAmount()) {
            throw new Mage_Payment_Model_Info_Exception(
                Mage::helper('capayable')->__('There was an error during commmunication with Capayable') . ": " .
                Mage::helper('capayable')->__($result->getRefuseReason()) . " " .
                Mage::helper('capayable')->__('For additional information contact Capayable on +31 40 - 259 5072.')
            );
        }

        // okay, can we get an FirstInstallmentAmount to cash with iDeal?
        // store both order increment id and firstInstallment in session
        $firstInstallment   = $result->getFirstInstallmentAmount();
        $session            = Mage::getSingleton('checkout/session');
        $session->setFirstInstallment($firstInstallment);
        $session->setIncrId($order->getIncrementId());
        if($this->_logging) {
            Mage::log('authorize (Payinterms) firstInstallmentAmount (in cents) '.$firstInstallment, null, $this->_logfile);
            Mage::log('authorize (Payinterms) TransactionNumber '.$result->getTransactionNumber(), null, $this->_logfile);
        }

        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, false);
        $order->save();
        $order->setState(
            Mage::helper('capayable')->getOrderStatus($order->getStore()),
            true,
            Mage::helper('capayable')->__('Order is authorized by Capayable with transaction ID %s',$result->getTransactionNumber()),
            null,
            false);

        // Set magento transaction id which returned from capayable
        $payment->setLastTransId($result->getTransactionNumber());

        return $this;
    }

    /**
     * Assign data to info model instance
     * Assign data to info model instance
     * Save capayable customer
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if($this->_logging) {
            Mage::log('In assignData (Payinterms)', null, $this->_logfile);
            Mage::log($data, null, $this->_logfile);
        }

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $quote = $this->getInfoInstance()->getQuote();
        $address = $quote->getBillingAddress();

        if(!$quote->getCustomerMiddlename()) {
            $quote->setCustomerMiddlename($data->getCustomerMiddlename());
        }
        if(!$quote->getCustomerGender()) {
            $quote->setCustomerGender($data->getCustomerGender());
        }

        // Convert date format
        $dob = $quote->getCustomerDob() ? $quote->getCustomerDob() : $data->getCustomerDob();
        $dob = Mage::app()->getLocale()->date($dob, null, null, false)->toString('yyyy-MM-dd 00:00:00');
        $data->setCustomerDob($dob);
        $quote->setCustomerDob($dob);

        $capayableCustomer = Mage::getModel('capayable/customer')->loadByEmail($quote->getCustomerEmail());

        /**
         * If capayable customer doesn't exist fill new customer data from quote data.
         * Otherwise rewrite saved customer fields from form data.
         */
        if(!$capayableCustomer->getId()) {
            $capayableCustomer
                ->setCustomerEmail($quote->getCustomerEmail())
                ->setCustomerLastname($quote->getCustomerLastname())
                ->setCustomerMiddlename($quote->getCustomerMiddlename())
                ->setCustomerGender($quote->getCustomerGender())
                ->setCustomerDob($quote->getCustomerDob())
                ->setStreet($data->getStreet())
                ->setHouseNumber((int) $data->getHouseNumber())
                ->setHouseSuffix($data->getHouseSuffix())
                ->setPostcode($data->getPostcode())
                ->setCity($data->getCity())
                ->setCountryId($address->getCountryId())
                ->setTelephone($address->getTelephone())
                ->setFax($address->getFax())
                ->setIsCorporation($data->getIsCorporation())
                ->setIsSoleProprietor($data->getIsSoleProprietor())
                ->setCorporationName($data->getCorporationName())
                ->setCocNumber($data->getCocNumber())
                ->setIsInThreeInstallments(true);

        } else {
            $capayableCustomer->addData($data->getData());
            $capayableCustomer->setIsInThreeInstallment(true);
        }

        // Validate capayable customer required fields
        $result = $capayableCustomer->validate();

        if (true !== $result && is_array($result)) {
            throw new Mage_Payment_Model_Info_Exception(implode(', ', $result));
        }

        // Save capayable customer to 'capayable/customer' table
        $capayableCustomer->save();

        $this->getInfoInstance()->addData($data->getData());

        return $this;
    }

    /**
     * Customer credit check with Capayable.
     *
     * @param Tritac_Capayable_Model_Customer $_customer
     * @param $amount
     * @param bool $isFinal
     * @return \Swagger\Client\Model\CreditCheckResult
     */
    public function checkCredit(Tritac_Capayable_Model_Customer $_customer, $amount, $isFinal = false)
    {
        if($this->_logging){
            Mage::log('In checkCredit (Payinterms)', null, $this->_logfile);
        }

        $v2Model    = new Tritacv2_Model_CreditCheckRequestV2Model();

        $v2Model->setLastName($_customer->getCustomerLastname());
        $v2Model->setInitials($_customer->getCustomerMiddlename());

        $gender     = self::UNKNOWN;
        if($_customer->getCustomerGender() == 1) {
            $gender = self::MALE;
        }elseif($_customer->getCustomerGender() == 2) {
            $gender = self::FEMALE;
        }
        $v2Model->setGender($gender);

        $v2Model->setBirthDate($_customer->getCustomerDob());
        $v2Model->setStreetName($_customer->getStreet());
        $v2Model->setHouseNumber($_customer->getHouseNumber());
        $v2Model->setHouseNumberSuffix($_customer->getHouseSuffix());
        $v2Model->setZipCode($_customer->getPostcode());
        $v2Model->setCity($_customer->getCity());
        $v2Model->setCountryCode($_customer->getCountryId());

        $v2Model->setHasDifferentShippingAddress(false);
        try {
            $quote = Mage::getModel('checkout/session')->getQuote();
            if(isset($quote)) {
                $shippingData   = $quote->getShippingAddress()->getData();
                $billingData    = $quote->getBillingAddress()->getData();
                if(isset($shippingData) && isset($billingData)) {
                    if(isset($shippingData['postcode']) && isset($shippingData['street']) && isset($billingData['street'])) {
                        // okay, is there any difference then it would most certainly be in street
                        if($shippingData['street'] == $billingData['street']) {
                            // no diff so skip
                        } else {
                            $shpPostcode = $shippingData['postcode'];
                            $shpStreet  = $shippingData['street'];
                            $city       = $shippingData['city'];
                            $countryId  = $shippingData['country_id'];
                            $street     = '';
                            $houseNr    = '';
                            $housenrSfx = '';
                            $strData    = explode("\n", $shpStreet);
                            if (count($strData) == 3) {
                                $street     = $strData[0];
                                $houseNr    = $strData[1];
                                $housenrSfx = $strData[2];
                            } elseif(count($strData) == 2) {
                                $street     = $strData[0];
                                $pattern    = '#^([0-9]{1,5})([a-z0-9 \-/]{0,})$#i';
                                preg_match($pattern, $strData[1], $houseNumbers);
                                $houseNr    = $houseNumbers[1];
                                $housenrSfx = (isset($houseNumbers[2])) ? $houseNumbers[2] : '';
                            } elseif(count($strData) == 1 && strlen($shpStreet) > 0) {
                                $pattern = '#^([a-z0-9 [:punct:]\']*) ([0-9]{1,5})([a-z0-9 \-/]{0,})$#i';
                                preg_match($pattern, $shpStreet, $addressParts);
                                if(count($addressParts) > 1) {
                                    $street     = $addressParts[1];
                                    $houseNr    = $addressParts[2];
                                    $housenrSfx = (isset($addressParts[3])) ? $addressParts[3] : '';
                                }
                            }
                            if($this->_logging) {
                                Mage::log('street is now ' . $street, null, $this->_logfile);
                                Mage::log('housenr is now ' . $houseNr, null, $this->_logfile);
                                Mage::log('housesfx is now ' . $housenrSfx, null, $this->_logfile);
                            }
                            $v2Model->setHasDifferentShippingAddress(true);
                            $v2Model->setShippingStreetName($street);
                            $v2Model->setShippingHouseNumber($houseNr);
                            $v2Model->setShippingHouseNumberSuffix($housenrSfx);
                            $v2Model->setShippingZipCode($shpPostcode);
                            $v2Model->setShippingCity($city);
                            $v2Model->setShippingCountryCode($countryId);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log('Exception when trying to get ShippingAddress: ' . $e->getMessage(), null, $this->_logfile);
        }

        $v2Model->setPhoneNumber($_customer->getTelephone());
        $v2Model->setFaxNumber($_customer->getFax());
        $v2Model->setEmailAddress($_customer->getCustomerEmail());

        $v2Model->setIsCorporation((bool)$_customer->getIsCorporation());
        $v2Model->setCorporationName($_customer->getCorporationName());
        $v2Model->setCocNumber($_customer->getCocNumber());

        // Set to true in case of a small business / freelancer / independent contractor etc (zzp/eenmanszaak)
        $v2Model->setIsSoleProprietor((bool)$_customer->getIsSoleProprietor());

        $v2Model->setIsFinal($isFinal);
        $v2Model->setClaimAmount($amount);
        $v2Model->setIsInThreeInstallments(true);

        //apparently they want IP address now
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $v2Model->setIpAddress($ip);

        try {
            $publicKey      = Mage::getStoreConfig('payment/capayable/public_key');
            $apiConfig      = new Tritacv2_Configuration();
            $apiConfig->setApiKey('apikey', $publicKey); // test key 'f2d2a2aee085bfcde02d3b50e30a7394efcd49e5'
            if($this->_logging) {
                Mage::log('In checkCredit (Payinterms) dit is url ' . $this->_url, null, $this->_logfile);
            }
            $apiConfig->setHost($this->_url);
            $apiClient      = new Tritacv2_ApiClient($apiConfig);
            $creditCheckApi = new Tritacv2_Api_CreditCheckApi($apiClient);
            $result         = $creditCheckApi->creditCheckV2Post($v2Model);
            if($this->_logging) {
                Mage::log('In checkCredit (Payinterms) result : ', null, $this->_logfile);
                Mage::log($result, null, $this->_logfile);
            }
            return $result;
        } catch (Exception $e) {
            Mage::log('Exception when calling creditCheckApi->creditCheckV2Post: '. $e->getMessage(), null, $this->_logfile);
        }
    }

    /**
     * Post new invoice to Capayable
     *
     * @param $invoice
     * @return mixed
     */
    public function processApiInvoice($invoice)
    {
        if($this->_logging){
            Mage::log('In processApiInvoice (Payinterms)', null, $this->_logfile);
        }
        $order      = $invoice->getOrder();

        // Request model, data model, product line model, total line model
        $v2RqstMdl      = new Tritacv2_Model_InvoiceRequestV2Model();
        $v2DataMdl      = new Tritacv2_Model_InvoicePdfDataModel();
        $v2ProdMdlArray = $this->getProductLines($order);
        $v2TtlMdlArray  = $this->getOrderTotals($order);
        $shopName       = Mage::app()->getStore()->getFrontendName();
        $v2DataMdl->setShopName($shopName);
        $v2DataMdl->setDescription('');
        $v2DataMdl->setProductLines($v2ProdMdlArray);
        $v2DataMdl->setTotalLines($v2TtlMdlArray);
        // finally fill request model:
        $v2RqstMdl->setTransactionNumber($invoice->getTransactionId());
        $v2RqstMdl->setInvoiceNumber($invoice->getIncrementId());
        $objDateTime = new DateTime('NOW');
        $v2RqstMdl->setInvoiceDate($objDateTime);
        $v2RqstMdl->setInvoiceAmount(Mage::helper('capayable')->convertToCents($invoice->getGrandTotal()));
        $v2RqstMdl->setInvoiceDescription(Mage::helper('capayable')->__('Order').' '.$order->getIncrementId());
        $v2RqstMdl->setInvoicePdfSubmitType('INCLUDED_DATA');
        $v2RqstMdl->setCultureCode('nl-NL');
        $v2RqstMdl->setInvoicePdfData($v2DataMdl);
        try {
            $publicKey      = Mage::getStoreConfig('payment/capayable/public_key');
            $apiConfig      = new Tritacv2_Configuration();
            $apiConfig->setApiKey('apikey', $publicKey);
            if($this->_logging) {
                Mage::log('In processApiInvoice (Payinterms) dit is url ' . $this->_url, null, $this->_logfile);
            }
            $apiConfig->setHost($this->_url);
            $apiClient      = new Tritacv2_ApiClient($apiConfig);
            $invoiceApi     = new Tritacv2_Api_InvoiceApi($apiClient);
            $invoiceRslts   = $invoiceApi->invoiceV2Post($v2RqstMdl); // $invoiceRslts is of type Tritacv2_Model_InvoiceResult
            if($this->_logging) {
                Mage::log('In processApiInvoice (Payinterms) result : ', null, $this->_logfile);
                Mage::log($invoiceRslts, null, $this->_logfile);
            }
            $results = array();
            $results['IsAccepted'] = $invoiceRslts->getIsAccepted();
            $results['PaymentUrl'] = $invoiceRslts->getPaymentUrl();
            return $results;
        } catch (Exception $e) {
            Mage::log('Exception when calling creditCheckApi->creditCheckV2Post: '. $e->getMessage(), null, $this->_logfile);
        }
    }

    public function getProductLines($order)
    {
        $prodLines  = array();
        $items      = $order->getAllVisibleItems();
        foreach($items as $item) {
            $v2ProdMdl  = new Tritacv2_Model_InvoicePdfDataProductLineModel();
            $product    = Mage::getModel('catalog/product')->load($item->getProductId());
            $sku        = $product->getSku();
            $name       = $product->getName();
            $qty        = $item->getQtyOrdered();
            $price      = $product->getPrice();
            $lnTtl      = $price * $qty;
            $frmtPrice  = Mage::helper('core')->currency($price, true, false);
            $frmtLnTtl  = Mage::helper('core')->currency($lnTtl, true, false);
            $v2ProdMdl->setProductCode($sku);
            $v2ProdMdl->setProductName($name);
            $v2ProdMdl->setQuantity((string)$qty);
            $v2ProdMdl->setPrice($frmtPrice);
            $v2ProdMdl->setLineTotal($frmtLnTtl);
            array_push($prodLines, $v2ProdMdl);
        }
        return $prodLines;
    }

    public function getOrderTotals($order)
    {
        $totals     = array();
        // subtotaal
        $v2TtlMdl1  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
        $subtotal   = Mage::helper('core')->currency($order->getSubtotal(), true, false);
        $v2TtlMdl1->setName('Subtotaal');
        $v2TtlMdl1->setValue($subtotal);
        $v2TtlMdl1->setIsTotal(false);
        array_push($totals, $v2TtlMdl1);

        // verzending
        $v2TtlMdl2  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
        $shipping   = Mage::helper('core')->currency($order->getShippingAmount(), true, false);
        $v2TtlMdl2->setName('Verzendkosten');
        $v2TtlMdl2->setValue($shipping);
        $v2TtlMdl2->setIsTotal(false);
        array_push($totals, $v2TtlMdl2);

        // korting (indien > 0)
        if($order->getDiscountAmount() > 0){
            $v2TtlMdl3  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
            $discount   = Mage::helper('core')->currency($order->getDiscountAmount(), true, false);
            $v2TtlMdl3->setName('Korting');
            $v2TtlMdl3->setValue($discount);
            $v2TtlMdl3->setIsTotal(false);
            array_push($totals, $v2TtlMdl3);
        }

        // capayable fee
        $v2TtlMdl3a  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
        $capFee      = Mage::helper('core')->currency($order->getCapayableFee(), true, false);
        $v2TtlMdl3a->setName('Capayable fee');
        $v2TtlMdl3a->setValue($capFee);
        $v2TtlMdl3a->setIsTotal(false);
        array_push($totals, $v2TtlMdl3a);

        // btw
        $v2TtlMdl4  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
        $tax        = Mage::helper('core')->currency($order->getTaxAmount(), true, false);
        $v2TtlMdl4->setName('BTW');
        $v2TtlMdl4->setValue($tax);
        $v2TtlMdl4->setIsTotal(false);
        array_push($totals, $v2TtlMdl4);

        // totaal
        $v2TtlMdl5  = new Tritacv2_Model_InvoicePdfDataTotalLineModel();
        $grandTotal = Mage::helper('core')->currency($order->getGrandTotal(), true, false);
        $v2TtlMdl5->setName('Totaal');
        $v2TtlMdl5->setValue($grandTotal);
        $v2TtlMdl5->setIsTotal(true);
        array_push($totals, $v2TtlMdl5);

        return $totals;
    }

    //before refund
    /*
    public function processBeforeRefund($invoice, $payment)
    {
    }
    */

    // Refund api
    public function refund(Varien_Object $payment, $amount)
    {
        if($this->_logging){
            Mage::log('In refund (Payinterms) amount coming in '.$amount, null, $this->_logfile);
        }
        $transactionNr  = $payment->getLastTransId();
        $returnNr       = $payment->getOrder()->getIncrementId();
        // Convert amount to cents
        $amount         = Mage::helper('capayable')->convertToCents($amount);
        $v2CrdRqstMdl   = new Tritacv2_Model_InvoiceCreditRequestV2Model();
        $v2CrdRqstMdl->setTransactionNumber($transactionNr);
        $v2CrdRqstMdl->setReturnNumber($returnNr);
        $v2CrdRqstMdl->setCreditAmount($amount);

        try {
            $publicKey      = Mage::getStoreConfig('payment/capayable/public_key');
            $apiConfig      = new Tritacv2_Configuration();
            $apiConfig->setApiKey('apikey', $publicKey);
            $apiConfig->setHost($this->_url);
            $apiClient      = new Tritacv2_ApiClient($apiConfig);
            $invCrdtApi     = new Tritacv2_Api_InvoiceCreditApi($apiClient);
            $rslts          = $invCrdtApi->invoiceCreditV2Post($v2CrdRqstMdl);
            if($this->_logging){
                Mage::log('In refund results: ', null, $this->_logfile);
                Mage::log($rslts, null, $this->_logfile);
            }
            return $this;
        } catch (Exception $e) {
            Mage::log('Exception when calling invCrdtApi->invoiceCreditV2Post: '. $e->getMessage(), null, $this->_logfile);
        }
    }

    //after refund
    /*
    public function processCreditmemo($creditmemo, $payment)
    {
    }
    */


    /**
     * Deze functie wordt opgeroepen door Tritac_Capayable_Model_Quote_Address_Total::collect gedurende de checkout
     * De bedoeling is dat deze de correcte prijs teruggeeft in samenwerking met de Helper
     *
     */
    public function getAddressCapayableFee(Mage_Sales_Model_Quote_Address $address, $value = NULL, $alreadyExclTax = FALSE){
        if($this->_logging) {
            Mage::log('In getAddressCapayableFee (Payinterms)', null, $this->_logfile);
        }
        if (is_null($value)){
            $value = floatval(Mage::helper('capayable')->getPaymentMethodCost($this->_code,$address));
        }
        if (Mage::helper('capayable')->capayableFeePriceIncludesTax()) {
            if (!$alreadyExclTax) {
                $value = Mage::helper('capayable')->getCapayableFeePrice($value, false, $address, $address->getQuote()->getCustomerTaxClassId());
            }
        }
        if($this->_logging) {
            Mage::log('getAddressCapayableFee (Payinterms) value '.$value, null, $this->_logfile);
        }
        return $value;
    }

    public function getCapayableTaxAmount(Mage_Sales_Model_Quote_Address $address, $value = NULL, $alreadyExclTax = FALSE){
        if($this->_logging) {
            Mage::log('In getCapayableTaxAmount (Payinterms)', null, $this->_logfile);
        }
        if (is_null($value)){
            $value = floatval(Mage::helper('capayable')->getPaymentMethodCost($this->_code, $address));
        }
        //die(print '$address->getQuote()->getCustomerTaxClassId() = '.$address->getQuote()->getCustomerTaxClassId());
        if (Mage::helper('capayable')->capayableFeePriceIncludesTax()) {
            $includingTax = Mage::helper('capayable')->getCapayableFeePrice($value, true, $address, $address->getQuote()->getCustomerTaxClassId());
            if (!$alreadyExclTax) {
                $value = Mage::helper('capayable')->getCapayableFeePrice($value, false, $address, $address->getQuote()->getCustomerTaxClassId());
            }
            if($this->_logging) {
                Mage::log('getCapayableTaxAmount (Payinterms) includingTax '.$includingTax.' - '.$value, null, $this->_logfile);
            }
            return $includingTax - $value;
        }
        if($this->_logging) {
            Mage::log('getCapayableTaxAmount (Payinterms) return 0', null, $this->_logfile);
        }
        return 0;
    }

    public function getAddressCosts(Mage_Customer_Model_Address_Abstract $address){
        return floatval(Mage::helper('capayable')->getPaymentMethodCost($this->_code,$address));
    }

    public function getOrderPlaceRedirectUrl() {
        if($this->_logging){
            Mage::log('getOrderPlaceRedirectUrl ', null, $this->_logfile);
        }
        $session    = Mage::getSingleton('checkout/session');
        if ($session->getIncrId()) {
            if($this->_logging){
                Mage::log('Order found in session, orderId: ' . $session->getIncrId(), null, $this->_logfile);
            }
            $order      = Mage::getModel('sales/order')->loadByIncrementId($session->getIncrId());
            $payment    = $order->getPayment();
            $firstInst  = $session->getFirstInstallment();
            if ($order->canInvoice()){
                if($this->_logging){
                    Mage::log('getOrderPlaceRedirectUrl can invoice 1', null, $this->_logfile);
                }
                try {
                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    // Set magento transaction id which returned from capayable
                    $invoice->setTransactionId($payment->getLastTransId());

                    // Allow payment capture and register new magento transaction
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

                    // Register invoice and apply it to order, order items etc.
                    //$invoice->addComment('U betaald via gespreid betalen.', true, true);
                    //$invoice->getOrder()->setCustomerNoteNotify(true);
                    $invoice->register();
                    $invoice->setEmailSent(true);
                    $invoice->getOrder()->setIsInProcess(true);

                    $transaction = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());

                    // Commit changes or rollback if error has occurred
                    $transaction->save();
                    $order->sendNewOrderEmail();
                    /**
                     * Register invoice with Capayable
                     */
                    $response = $this->processApiInvoice($invoice);
                    // hoe ziet is accepted eruit?
                    if($this->_logging) {
                        Mage::log('getOrderPlaceRedirectUrl isApiInvoiceAccepted', null, $this->_logfile);
                        Mage::log($response, null, $this->_logfile);
                    }
                    if ($response['IsAccepted'] == 1) {
                        $invoice->sendEmail(true);
                    } else {
                        $this->_getSession()->addError(Mage::helper('capayable')->__('Failed to send the invoice.'));
                    }
                } catch (Exception $ex) {
                    Mage::log('getOrderPlaceRedirectUrl error '.$ex, null, $this->_logfile);
                }
                // blijkbaar moet het iets zijn van
                // https: //capayable-payment.tritac.com/payinstallment/2968877e
                // 4d47412cb1fcd833d3e7e37d?returnurl=http%3A%2F%2Fyourshop.nl%2Fpayreturn%2F42
                $successUrl2    = Mage::getUrl('checkout/onepage/success');
                $cancelUrl2     = Mage::getUrl('customerordercancel/order/cancel', array('order_id' => $order->getId(), '_secure'=>true));
                if($this->_logging) {
                    Mage::log('getOrderPlaceRedirectUrl success url: ', null, $this->_logfile);
                    Mage::log($successUrl2, null, $this->_logfile);
                    Mage::log('getOrderPlaceRedirectUrl cancel url: ', null, $this->_logfile);
                    Mage::log($cancelUrl2, null, $this->_logfile);
                }
                //$urlEncodedSuccesUrl = urlencode($successUrl2);
                return $response['PaymentUrl'].'?returnurl='.$successUrl2.'&shopOrderExchangeUrl='.$cancelUrl2; //.$urlEncodedSuccesUrl;
                //return Mage::getUrl('capayable/checkout/redirect');
            }
        } else {
            // something went wrong
            Mage::log('getOrderPlaceRedirectUrl could not invoice', null, $this->_logfile);
        }
    }
}