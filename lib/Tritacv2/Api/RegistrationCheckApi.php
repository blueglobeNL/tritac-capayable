<?php
/**
 * RegistrationCheckApi
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
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
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */


/**
 * RegistrationCheckApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Tritacv2_Api_RegistrationCheckApi
{
    /**
     * API Client
     *
     * @var \Swagger\Client\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \Swagger\Client\ApiClient|null $apiClient The api client to use
     */
    public function __construct(Tritacv2_ApiClient $apiClient = null)
    {
        if ($apiClient === null) {
            $apiClient = new Tritacv2_ApiClient();
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \Swagger\Client\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \Swagger\Client\ApiClient $apiClient set the API client
     *
     * @return RegistrationCheckApi
     */
    public function setApiClient(Tritacv2_ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation registrationCheckV2Post
     *
     * # Chamber of Commerce registration check  If you sell to businesses, the shop can do a registration check of the corporation’s chamber of commerce number. The API will return the address data if the number is found.  This option may or may not be available, dependent on your contract with Capayable, as it entails an extra cost.     Note: the CoC check on the live environment may take a while, so be sure to set longer timeouts (max 2 minutes).    Supported countries:  - Test environment: NL, BE, DE  - Live environment: NL, BE, CH, CZ, DE, DK, ES, FI, FR, GB, IE, IS, IT, LT, LU, MT, NO, PL, PT, SE, SK, US
     *
     * @param \Swagger\Client\Model\RegistrationCheckRequestV2Model $model  (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\RegistrationCheckResult
     */
    public function registrationCheckV2Post($model)
    {
        // verify the required parameter 'model' is set
        if ($model === null) {
            throw new \InvalidArgumentException('Missing the required parameter $model when calling registrationCheckV2Post');
        }
        // parse inputs
        $resourcePath   = "/v2/registrationcheck";
        $httpBody       = '';
        $queryParams    = [];
        $headerParams   = [];
        $formParams     = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json', 'text/json', 'text/xml']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json', 'text/json', 'text/xml', 'application/x-www-form-urlencoded']);

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($model)) {
            $_tempBody = $model;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires API key authentication
        $apiKey = $this->apiClient->getApiKeyWithPrefix('apikey');
        if (strlen($apiKey) !== 0) {
            $headerParams['apikey'] = $apiKey;
        }
        // make the API Call
        try {
            $registrationCheckResult = new Tritacv2_Model_RegistrationCheckResult();
            $result = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                $registrationCheckResult,
                '/v2/registrationcheck'
            );

            //return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\RegistrationCheckResult', $httpHeader), $statusCode, $httpHeader];
            $response   = $result[0];
            $statusCode = $result[1];
            $httpHeader = $result[2];
            $registrationCheckResult->setIsAccepted($response->IsAccepted);
            $registrationCheckResult->setLastName($response->LastName);
            $registrationCheckResult->setInitials($response->Initials);
            $registrationCheckResult->setGender($response->Gender);
            $registrationCheckResult->setStreetName($response->StreetName);
            $registrationCheckResult->setHouseNumber($response->HouseNumber);
            $registrationCheckResult->setHouseNumberSuffix($response->HouseNumberSuffix);
            $registrationCheckResult->setZipCode($response->ZipCode);
            $registrationCheckResult->setCity($response->City);
            $registrationCheckResult->setCountryCode($response->CountryCode);
            $registrationCheckResult->setPhoneNumber($response->PhoneNumber);
            $registrationCheckResult->setFaxNumber($response->FaxNumber);
            $registrationCheckResult->setEmailAddress($response->EmailAddress);
            $registrationCheckResult->setIsCorporation($response->IsCorporation);
            $registrationCheckResult->setIsSoleProprietor($response->IsSoleProprietor);
            $registrationCheckResult->setCorporationName($response->CorporationName);
            $registrationCheckResult->setCoCNumber($response->CoCNumber);

            return $registrationCheckResult;

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\RegistrationCheckResult', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }
}
