<?php
class Tritac_CapayableApiClient_Client {

    private $environment;
    private $apiUrl;
    private $apiKey;        // public
    private $apiSecret;     // secret

    const ACC_URL 			        = 'http://capayable-api-acc.tritac.com';
    const TEST_URL 			        = 'https://capayable-api-test.tritac.com';
    const PROD_URL 			        = 'https://capayable-api.tritac.com';

    const VERSION_PATH 		        = '/v2';

    const CREDITCHECK_PATH          = '/creditcheck';
    const INVOICE_PATH              = '/invoice';
    const INVOICECREDIT_PATH        = '/invoicecredit';
    const REGISTRATIONCHECK_PATH    = '/registrationcheck';

    protected $logging             = true; // true enables logging to specific file
    protected $logfile;

    public function __construct($apiKey, $apiSecret, $env = null)
    {
        if($env == null || $env == Tritac_CapayableApiClient_Enums_Environment::PROD) {
            $this->apiUrl = self::PROD_URL;
        } elseif($env == Tritac_CapayableApiClient_Enums_Environment::TEST) {
            $this->apiUrl = self::TEST_URL;
        } elseif($env == Tritac_CapayableApiClient_Enums_Environment::TEST) {
            $this->apiUrl = self::ACC_URL;
        }
        $date               = date('Y-m-d');
        $this->logfile     = 'capayable_'.$date.'.log';
        $this->environment  = $env;
        $this->apiKey       = $apiKey;
        $this->apiSecret    = $apiSecret;
    }

    public function doCreditCheck(Tritac_CapayableApiClient_Models_CreditCheckRequest $request)
	{
		$args = $request->toArray();
		if(!$request->getIsCorporation()){
			unset($args['CocNumber']);
			unset($args['CorporationName']);
			unset($args['IsSoleProprietor']);
		}

		$path = self::VERSION_PATH . self::CREDITCHECK_PATH;

		$response = json_decode($this->makeRequest(Tritac_CapayableApiClient_Enums_HttpMethod::GET, $path, $this->buildQueryString($args, $path)), true);

        if($this->_logging) {
            Mage::log('client doCreditCheck response object: ', null, $this->_logfile);
            Mage::log($response, null, $this->_logfile);
        }

		$creditCheckResponse = new Tritac_CapayableApiClient_Models_CreditCheckResponse();

		if( $response['IsAccepted'] ){
			$creditCheckResponse->setAccepted($response['TransactionNumber']);
            if( $response['FirstInstallmentAmount'] ){
                $creditCheckResponse->setFirstInstallmentAmount($response['FirstInstallmentAmount']);
            }
		}else{
			$creditCheckResponse->setRefused($response['RefuseReason'], $response['RefuseContactName'], $response['RefuseContactPhoneNumber']);
		}

		return $creditCheckResponse;
	}

	public function doRegistrationCheck(Tritac_CapayableApiClient_Models_RegistrationCheckRequest $request)
	{
		$args = $request->toArray();

		$path = self::VERSION_PATH . self::REGISTRATIONCHECK_PATH;

		$response = json_decode($this->makeRequest(Tritac_CapayableApiClient_Enums_HttpMethod::GET, $path, $this->buildQueryString($args, $path)), true);

		$creditCheckResponse = new Tritac_CapayableApiClient_Models_RegistrationCheckResponse($response['IsAccepted'], $response['HouseNumber'],
			$response['HouseNumberSuffix'], $response['ZipCode'], $response['City'], $response['CountryCode'],
			$response['PhoneNumber'], $response['CorporationName'], $response['CoCNumber'], $response['StreetName']);

		return $creditCheckResponse;
	}

	public function registerInvoice(Tritac_CapayableApiClient_Models_Invoice $invoice)
	{
		$args = $invoice->toArray();
		$path = self::VERSION_PATH . self::INVOICE_PATH;

		$response = json_decode($this->makeRequest(Tritac_CapayableApiClient_Enums_HttpMethod::GET, $path, $this->buildQueryString($args, $path)), true);

		//return $response['IsAccepted'];
        return $response;
	}

    public function creditInvoice(Tritac_CapayableApiClient_Models_InvoiceCreditRequest $request)
	{
		$args = $request->toArray();
		$path = self::VERSION_PATH . self::INVOICECREDIT_PATH;

		$response = json_decode($this->makeRequest(Tritac_CapayableApiClient_Enums_HttpMethod::GET, $path, $this->buildQueryString($args, $path)), true);

		$invoiceCreditResponse = new Tritac_CapayableApiClient_Models_InvoiceCreditResponse($response['Result'], $response['AmountCredited'], $response['AmountNotCredited']);

		return $invoiceCreditResponse;
	}

    /* Private methods */

    private function makeRequest($method, $url, $queryString = '', $content = null)
    {
        $request = curl_init();

        // Create the required Http headers
        $headers = $this->buildHeaders($method, $content);

        if(self::USE_FIDDLER)
        {
            // We use this to redirect the request through a local proxy and trace it with fiddler
            curl_setopt($request, CURLOPT_PROXY, self::FIDDLER_PROXY);
        }

        // Set the Url
        curl_setopt($request, CURLOPT_URL, $this->apiUrl . $url . $queryString);

        // Add the headers and hmac auth.
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);

        // Return the response as a string
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        // Set custom request method because curl has no setting for PUT and DELETE
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);

        // Make the headers accessible for debugging purposes
        curl_setopt($request, CURLINFO_HEADER_OUT, true);

        // Point curl to the correct certificate.
        // See: http://stackoverflow.com/questions/6400300/php-curl-https-causing-exception-ssl-certificate-problem-verify-that-the-ca-cer
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($request, CURLOPT_CAINFO, $this->certificate);

        // If we have a request body send it too
        if(strlen($content) > 0) {
            if($this->_logging) {
                Mage::log('makeRequest content object: ', null, $this->_logfile);
                Mage::log($content, null, $this->_logfile);
            }
            curl_setopt($request, CURLOPT_POSTFIELDS, $content);
        }

        // Capayable API doesn't handle IPv6 requests well, so add this line and resolve to IPv4
        curl_setopt($request, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // Make the request
        $response = curl_exec($request);

        // Get the status code
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);



        // Check for errors
        // First we check if the response is missing which will probably be caused by a cURL error
        // After this the check if there are not HTTP errors (status codes other than 200-206)
        if ($response === false)
        {
            $error = curl_error($request);
            curl_close($request);
            throw new Exception('cURL error: ' . $error);
        }
        else if($status < 200 || $status > 206)
        {
            $headers = curl_getinfo($request, CURLINFO_HEADER_OUT);
            $message = json_decode($response);

            curl_close($request);

            throw new Exception('Output headers: '. "\n" . $headers ."\n\n".
                                'Content: ' . $content ."\n\n".
                                'Unexpected status code [' . $status . ']. The server returned the following message: "' . $message->Message . '"');
        }
        else
        {
            curl_close($request);

            return $response;
        }
    }

    private function buildHeaders($method, $content = null)
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($content),
        );

        return $headers;
    }

    private function buildQueryString(array $args, $path)
    {

        $hash = $this->getHash($args, $path);
        $args['signature'] = $hash;
        $args['key'] = $this->apiKey;
        $args['timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        return $this->encodeQueryString($args);
    }

    private function getHash(array $args, $path){
        // Copy the array
        $sortedArgs = $args;

        // Sort it
        ksort($sortedArgs);

        $sortedArgs['key'] = $this->apiKey;
        $sortedArgs['timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

        $representation = $path . urldecode($this->encodeQueryString($sortedArgs));
        $hash = hash_hmac('sha256', $representation, $this->apiSecret, true);

        return base64_encode($hash);
    }

    private function encodeQueryString(array $args) {
        $queryString = (count($args) > 0) ? '?' . http_build_query($args) : '';

        // .Net does not seem to like the /?foo[0]=bar&foo[1]=baz notation so we
        // convert it to /?foo=bar&foo=baz
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);
    }
}