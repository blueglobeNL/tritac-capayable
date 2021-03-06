<?php
/**
 * CreditCheckResult
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Capayable API V2
 *
 * API for Pay after delivery and Pay in 3 installments
 *
 * OpenAPI spec version: v2
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

//namespace Swagger\Client\Model;

//use \ArrayAccess;

/**
 * CreditCheckResult Class Doc Comment
 *
 * @category    Class */
/**
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Tritacv2_Model_CreditCheckResult implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'CreditCheckResult';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'is_accepted'               => 'bool',
        'transaction_number'        => 'string',
        'refuse_reason'             => 'string',
        'refuse_contact_name'       => 'string',
        'refuse_contact_phone_number' => 'string',
        'first_installment_amount'  => 'int'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'is_accepted'               => 'IsAccepted',
        'transaction_number'        => 'TransactionNumber',
        'refuse_reason'             => 'RefuseReason',
        'refuse_contact_name'       => 'RefuseContactName',
        'refuse_contact_phone_number' => 'RefuseContactPhoneNumber',
        'first_installment_amount'  => 'FirstInstallmentAmount'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'is_accepted'               => 'setIsAccepted',
        'transaction_number'        => 'setTransactionNumber',
        'refuse_reason'             => 'setRefuseReason',
        'refuse_contact_name'       => 'setRefuseContactName',
        'refuse_contact_phone_number' => 'setRefuseContactPhoneNumber',
        'first_installment_amount'  => 'setFirstInstallmentAmount'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'is_accepted'               => 'getIsAccepted',
        'transaction_number'        => 'getTransactionNumber',
        'refuse_reason'             => 'getRefuseReason',
        'refuse_contact_name'       => 'getRefuseContactName',
        'refuse_contact_phone_number' => 'getRefuseContactPhoneNumber',
        'first_installment_amount'  => 'getFirstInstallmentAmount'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    const REFUSE_REASON_AMOUNT_EXCEEDS_LIMIT    = 'AMOUNT_EXCEEDS_LIMIT';
    const REFUSE_REASON_BALANCE_EXCEEDS_LIMIT   = 'BALANCE_EXCEEDS_LIMIT';
    const REFUSE_REASON_NOT_CREDITWORTHY        = 'NOT_CREDITWORTHY';
    const REFUSE_REASON_CREDITCHECK_UNAVAILABLE = 'CREDITCHECK_UNAVAILABLE';
    const REFUSE_REASON_NOT_FOUND               = 'NOT_FOUND';
    const REFUSE_REASON_ADDRESS_BLOCKED         = 'ADDRESS_BLOCKED';
    const REFUSE_REASON_TOO_YOUNG               = 'TOO_YOUNG';
    const REFUSE_REASON_DIFFERENT_SHIPPING_ADDRESS = 'DIFFERENT_SHIPPING_ADDRESS';
    const REFUSE_REASON_SHIPPING_ADDRESS_BLOCKED = 'SHIPPING_ADDRESS_BLOCKED';
    const REFUSE_REASON_IP_ADDRESS_BLOCKED      = 'IP_ADDRESS_BLOCKED';
    const REFUSE_REASON_COUNTRY_BLOCKED         = 'COUNTRY_BLOCKED';
    const REFUSE_REASON_SHIPPING_COUNTRY_BLOCKED = 'SHIPPING_COUNTRY_BLOCKED';
    const REFUSE_REASON_AMOUNT_TOO_LOW          = 'AMOUNT_TOO_LOW';
    const REFUSE_REASON_TOO_MANY_OPEN_INVOICES  = 'TOO_MANY_OPEN_INVOICES';
    const REFUSE_REASON_IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24_HRS = 'IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24HRS';
    

    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getRefuseReasonAllowableValues()
    {
        return [
            self::REFUSE_REASON_AMOUNT_EXCEEDS_LIMIT,
            self::REFUSE_REASON_BALANCE_EXCEEDS_LIMIT,
            self::REFUSE_REASON_NOT_CREDITWORTHY,
            self::REFUSE_REASON_CREDITCHECK_UNAVAILABLE,
            self::REFUSE_REASON_NOT_FOUND,
            self::REFUSE_REASON_ADDRESS_BLOCKED,
            self::REFUSE_REASON_TOO_YOUNG,
            self::REFUSE_REASON_DIFFERENT_SHIPPING_ADDRESS,
            self::REFUSE_REASON_SHIPPING_ADDRESS_BLOCKED,
            self::REFUSE_REASON_IP_ADDRESS_BLOCKED,
            self::REFUSE_REASON_COUNTRY_BLOCKED,
            self::REFUSE_REASON_SHIPPING_COUNTRY_BLOCKED,
            self::REFUSE_REASON_AMOUNT_TOO_LOW,
            self::REFUSE_REASON_TOO_MANY_OPEN_INVOICES,
            self::REFUSE_REASON_IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24_HRS,
        ];
    }
    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['is_accepted']         = isset($data['is_accepted']) ? $data['is_accepted'] : null;
        $this->container['transaction_number']  = isset($data['transaction_number']) ? $data['transaction_number'] : null;
        $this->container['refuse_reason']       = isset($data['refuse_reason']) ? $data['refuse_reason'] : null;
        $this->container['refuse_contact_name'] = isset($data['refuse_contact_name']) ? $data['refuse_contact_name'] : null;
        $this->container['refuse_contact_phone_number'] = isset($data['refuse_contact_phone_number']) ? $data['refuse_contact_phone_number'] : null;
        $this->container['first_installment_amount']    = isset($data['first_installment_amount']) ? $data['first_installment_amount'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        $allowed_values = ["AMOUNT_EXCEEDS_LIMIT", "BALANCE_EXCEEDS_LIMIT", "NOT_CREDITWORTHY", "CREDITCHECK_UNAVAILABLE", "NOT_FOUND", "ADDRESS_BLOCKED", "TOO_YOUNG", "DIFFERENT_SHIPPING_ADDRESS", "SHIPPING_ADDRESS_BLOCKED", "IP_ADDRESS_BLOCKED", "COUNTRY_BLOCKED", "SHIPPING_COUNTRY_BLOCKED", "AMOUNT_TOO_LOW", "TOO_MANY_OPEN_INVOICES", "IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24HRS"];
        if (!in_array($this->container['refuse_reason'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'refuse_reason', must be one of #{allowed_values}.";
        }

        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        $allowed_values = ["AMOUNT_EXCEEDS_LIMIT", "BALANCE_EXCEEDS_LIMIT", "NOT_CREDITWORTHY", "CREDITCHECK_UNAVAILABLE", "NOT_FOUND", "ADDRESS_BLOCKED", "TOO_YOUNG", "DIFFERENT_SHIPPING_ADDRESS", "SHIPPING_ADDRESS_BLOCKED", "IP_ADDRESS_BLOCKED", "COUNTRY_BLOCKED", "SHIPPING_COUNTRY_BLOCKED", "AMOUNT_TOO_LOW", "TOO_MANY_OPEN_INVOICES", "IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24HRS"];
        if (!in_array($this->container['refuse_reason'], $allowed_values)) {
            return false;
        }
        return true;
    }


    /**
     * Gets is_accepted
     * @return bool
     */
    public function getIsAccepted()
    {
        return $this->container['is_accepted'];
    }

    /**
     * Sets is_accepted
     * @param bool $is_accepted
     * @return $this
     */
    public function setIsAccepted($is_accepted)
    {
        $this->container['is_accepted'] = $is_accepted;

        return $this;
    }

    /**
     * Gets transaction_number
     * @return string
     */
    public function getTransactionNumber()
    {
        return $this->container['transaction_number'];
    }

    /**
     * Sets transaction_number
     * @param string $transaction_number
     * @return $this
     */
    public function setTransactionNumber($transaction_number)
    {
        $this->container['transaction_number'] = $transaction_number;

        return $this;
    }

    /**
     * Gets refuse_reason
     * @return string
     */
    public function getRefuseReason()
    {
        return $this->container['refuse_reason'];
    }

    /**
     * Sets refuse_reason
     * @param string $refuse_reason
     * @return $this
     */
    public function setRefuseReason($refuse_reason)
    {
        $allowed_values = array('AMOUNT_EXCEEDS_LIMIT', 'BALANCE_EXCEEDS_LIMIT', 'NOT_CREDITWORTHY', 'CREDITCHECK_UNAVAILABLE', 'NOT_FOUND', 'ADDRESS_BLOCKED', 'TOO_YOUNG', 'DIFFERENT_SHIPPING_ADDRESS', 'SHIPPING_ADDRESS_BLOCKED', 'IP_ADDRESS_BLOCKED', 'COUNTRY_BLOCKED', 'SHIPPING_COUNTRY_BLOCKED', 'AMOUNT_TOO_LOW', 'TOO_MANY_OPEN_INVOICES', 'IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24HRS');
        if (!is_null($refuse_reason) && (!in_array($refuse_reason, $allowed_values))) {
            throw new \InvalidArgumentException("Invalid value for 'refuse_reason', must be one of 'AMOUNT_EXCEEDS_LIMIT', 'BALANCE_EXCEEDS_LIMIT', 'NOT_CREDITWORTHY', 'CREDITCHECK_UNAVAILABLE', 'NOT_FOUND', 'ADDRESS_BLOCKED', 'TOO_YOUNG', 'DIFFERENT_SHIPPING_ADDRESS', 'SHIPPING_ADDRESS_BLOCKED', 'IP_ADDRESS_BLOCKED', 'COUNTRY_BLOCKED', 'SHIPPING_COUNTRY_BLOCKED', 'AMOUNT_TOO_LOW', 'TOO_MANY_OPEN_INVOICES', 'IP_ADDRESS_BLOCKED_MULTIPLE_ORDERS_WITHIN_24HRS'");
        }
        $this->container['refuse_reason'] = $refuse_reason;

        return $this;
    }

    /**
     * Gets refuse_contact_name
     * @return string
     */
    public function getRefuseContactName()
    {
        return $this->container['refuse_contact_name'];
    }

    /**
     * Sets refuse_contact_name
     * @param string $refuse_contact_name
     * @return $this
     */
    public function setRefuseContactName($refuse_contact_name)
    {
        $this->container['refuse_contact_name'] = $refuse_contact_name;

        return $this;
    }

    /**
     * Gets refuse_contact_phone_number
     * @return string
     */
    public function getRefuseContactPhoneNumber()
    {
        return $this->container['refuse_contact_phone_number'];
    }

    /**
     * Sets refuse_contact_phone_number
     * @param string $refuse_contact_phone_number
     * @return $this
     */
    public function setRefuseContactPhoneNumber($refuse_contact_phone_number)
    {
        $this->container['refuse_contact_phone_number'] = $refuse_contact_phone_number;

        return $this;
    }

    /**
     * Gets first_installment_amount
     * @return int
     */
    public function getFirstInstallmentAmount()
    {
        return $this->container['first_installment_amount'];
    }

    /**
     * Sets first_installment_amount
     * @param int $first_installment_amount
     * @return $this
     */
    public function setFirstInstallmentAmount($first_installment_amount)
    {
        $this->container['first_installment_amount'] = $first_installment_amount;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        $objSer = new Tritacv2_ObjectSerializer();
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode($objSer->sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode($objSer->sanitizeForSerialization($this));
    }
}
