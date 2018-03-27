<?php
/**
 * PartnerInvoiceCreditRequestV2Model
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

namespace Swagger\Client\Model;

use \ArrayAccess;

/**
 * PartnerInvoiceCreditRequestV2Model Class Doc Comment
 *
 * @category    Class */
/**
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class PartnerInvoiceCreditRequestV2Model implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'PartnerInvoiceCreditRequestV2Model';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'invoice_credit_request' => '\Swagger\Client\Model\InvoiceCreditRequestV2Model',
        'shop_name' => 'string'
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
        'invoice_credit_request' => 'InvoiceCreditRequest',
        'shop_name' => 'ShopName'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'invoice_credit_request' => 'setInvoiceCreditRequest',
        'shop_name' => 'setShopName'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'invoice_credit_request' => 'getInvoiceCreditRequest',
        'shop_name' => 'getShopName'
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
        $this->container['invoice_credit_request'] = isset($data['invoice_credit_request']) ? $data['invoice_credit_request'] : null;
        $this->container['shop_name'] = isset($data['shop_name']) ? $data['shop_name'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        if ($this->container['invoice_credit_request'] === null) {
            $invalid_properties[] = "'invoice_credit_request' can't be null";
        }
        if ($this->container['shop_name'] === null) {
            $invalid_properties[] = "'shop_name' can't be null";
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
        if ($this->container['invoice_credit_request'] === null) {
            return false;
        }
        if ($this->container['shop_name'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets invoice_credit_request
     * @return \Swagger\Client\Model\InvoiceCreditRequestV2Model
     */
    public function getInvoiceCreditRequest()
    {
        return $this->container['invoice_credit_request'];
    }

    /**
     * Sets invoice_credit_request
     * @param \Swagger\Client\Model\InvoiceCreditRequestV2Model $invoice_credit_request
     * @return $this
     */
    public function setInvoiceCreditRequest($invoice_credit_request)
    {
        $this->container['invoice_credit_request'] = $invoice_credit_request;

        return $this;
    }

    /**
     * Gets shop_name
     * @return string
     */
    public function getShopName()
    {
        return $this->container['shop_name'];
    }

    /**
     * Sets shop_name
     * @param string $shop_name
     * @return $this
     */
    public function setShopName($shop_name)
    {
        $this->container['shop_name'] = $shop_name;

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
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this));
    }
}
