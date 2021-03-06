<?php
/**
 * InvoiceCreditResult
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
 * InvoiceCreditResult Class Doc Comment
 *
 * @category    Class */
/**
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Tritacv2_Model_InvoiceCreditResult implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'InvoiceCreditResult';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'result'                => 'string',
        'amount_not_credited'   => 'int',
        'amount_credited'       => 'int'
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
        'result'                => 'Result',
        'amount_not_credited'   => 'AmountNotCredited',
        'amount_credited'       => 'AmountCredited'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'result'                => 'setResult',
        'amount_not_credited'   => 'setAmountNotCredited',
        'amount_credited'       => 'setAmountCredited'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'result'                => 'getResult',
        'amount_not_credited'   => 'getAmountNotCredited',
        'amount_credited'       => 'getAmountCredited'
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

    const RESULT_ACCEPTED           = 'ACCEPTED';
    const RESULT_EXCEEDS_PERIOD_LIMIT = 'EXCEEDS_PERIOD_LIMIT';
    const RESULT_ALREADY_PAID       = 'ALREADY_PAID';


    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getResultAllowableValues()
    {
        return [
            self::RESULT_ACCEPTED,
            self::RESULT_EXCEEDS_PERIOD_LIMIT,
            self::RESULT_ALREADY_PAID,
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
        $this->container['result'] = isset($data['result']) ? $data['result'] : null;
        $this->container['amount_not_credited'] = isset($data['amount_not_credited']) ? $data['amount_not_credited'] : null;
        $this->container['amount_credited'] = isset($data['amount_credited']) ? $data['amount_credited'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        $allowed_values = ["ACCEPTED", "EXCEEDS_PERIOD_LIMIT", "ALREADY_PAID"];
        if (!in_array($this->container['result'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'result', must be one of #{allowed_values}.";
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
        $allowed_values = ["ACCEPTED", "EXCEEDS_PERIOD_LIMIT", "ALREADY_PAID"];
        if (!in_array($this->container['result'], $allowed_values)) {
            return false;
        }
        return true;
    }


    /**
     * Gets result
     * @return string
     */
    public function getResult()
    {
        return $this->container['result'];
    }

    /**
     * Sets result
     * @param string $result
     * @return $this
     */
    public function setResult($result)
    {
        $allowed_values = array('ACCEPTED', 'EXCEEDS_PERIOD_LIMIT', 'ALREADY_PAID');
        if (!is_null($result) && (!in_array($result, $allowed_values))) {
            throw new \InvalidArgumentException("Invalid value for 'result', must be one of 'ACCEPTED', 'EXCEEDS_PERIOD_LIMIT', 'ALREADY_PAID'");
        }
        $this->container['result'] = $result;

        return $this;
    }

    /**
     * Gets amount_not_credited
     * @return int
     */
    public function getAmountNotCredited()
    {
        return $this->container['amount_not_credited'];
    }

    /**
     * Sets amount_not_credited
     * @param int $amount_not_credited
     * @return $this
     */
    public function setAmountNotCredited($amount_not_credited)
    {
        $this->container['amount_not_credited'] = $amount_not_credited;

        return $this;
    }

    /**
     * Gets amount_credited
     * @return int
     */
    public function getAmountCredited()
    {
        return $this->container['amount_credited'];
    }

    /**
     * Sets amount_credited
     * @param int $amount_credited
     * @return $this
     */
    public function setAmountCredited($amount_credited)
    {
        $this->container['amount_credited'] = $amount_credited;

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
