<?php
/**
 * InvoicePdfDataProductLineModel
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
 * InvoicePdfDataProductLineModel Class Doc Comment
 *
 * @category    Class */
/**
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Tritacv2_Model_InvoicePdfDataProductLineModel implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'InvoicePdfDataProductLineModel';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'product_code'  => 'string',
        'product_name'  => 'string',
        'quantity'      => 'string',
        'price'         => 'string',
        'line_total'    => 'string'
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
        'product_code'  => 'ProductCode',
        'product_name'  => 'ProductName',
        'quantity'      => 'Quantity',
        'price'         => 'Price',
        'line_total'    => 'LineTotal'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'product_code'  => 'setProductCode',
        'product_name'  => 'setProductName',
        'quantity'      => 'setQuantity',
        'price'         => 'setPrice',
        'line_total'    => 'setLineTotal'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'product_code'  => 'getProductCode',
        'product_name'  => 'getProductName',
        'quantity'      => 'getQuantity',
        'price'         => 'getPrice',
        'line_total'    => 'getLineTotal'
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
        $this->container['product_code']    = isset($data['product_code']) ? $data['product_code'] : null;
        $this->container['product_name']    = isset($data['product_name']) ? $data['product_name'] : null;
        $this->container['quantity']        = isset($data['quantity']) ? $data['quantity'] : null;
        $this->container['price']           = isset($data['price']) ? $data['price'] : null;
        $this->container['line_total']      = isset($data['line_total']) ? $data['line_total'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
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
        return true;
    }


    /**
     * Gets product_code
     * @return string
     */
    public function getProductCode()
    {
        return $this->container['product_code'];
    }

    /**
     * Sets product_code
     * @param string $product_code
     * @return $this
     */
    public function setProductCode($product_code)
    {
        $this->container['product_code'] = $product_code;

        return $this;
    }

    /**
     * Gets product_name
     * @return string
     */
    public function getProductName()
    {
        return $this->container['product_name'];
    }

    /**
     * Sets product_name
     * @param string $product_name
     * @return $this
     */
    public function setProductName($product_name)
    {
        $this->container['product_name'] = $product_name;

        return $this;
    }

    /**
     * Gets quantity
     * @return string
     */
    public function getQuantity()
    {
        return $this->container['quantity'];
    }

    /**
     * Sets quantity
     * @param string $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->container['quantity'] = $quantity;

        return $this;
    }

    /**
     * Gets price
     * @return string
     */
    public function getPrice()
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     * @param string $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets line_total
     * @return string
     */
    public function getLineTotal()
    {
        return $this->container['line_total'];
    }

    /**
     * Sets line_total
     * @param string $line_total
     * @return $this
     */
    public function setLineTotal($line_total)
    {
        $this->container['line_total'] = $line_total;

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