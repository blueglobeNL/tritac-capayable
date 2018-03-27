<?php
class Tritac_Capayable_AjaxController extends Mage_Core_Controller_Front_Action{

	public function registrationcheckAction() {

		$publicKey  = Mage::helper('capayable')->getPublicKey();
		if(Mage::helper('capayable')->getMode() == null || Mage::helper('capayable')->getMode() == 'production') {
            $url    = 'https://capayable-api.tritac.com';
        } else {
            $url    = 'https://capayable-api-test.tritac.com';
        }

        $v2Model    = new  Tritacv2_Model_RegistrationCheckRequestV2Model();

		$coc_number = $this->getRequest()->getParam("coc_number");
		if (!$coc_number) {
			$coc_number = 0;
		}

		$coc_number = intval($coc_number);
        $v2Model->setCoCNumber($coc_number);

        try {
            $apiConfig      = new Tritacv2_Configuration();
            $apiConfig->setApiKey('apikey', $publicKey);
            $apiConfig->setHost($url);
            $apiClient      = new Tritacv2_ApiClient($apiConfig);
            $regiCheckApi   = new Tritacv2_Api_RegistrationCheckApi($apiClient);
            $regiCheckRslts = $regiCheckApi->registrationCheckV2Post($v2Model);
            $arrayData = array();
            $arrayData['isAccepted']        = $regiCheckRslts->getIsAccepted();
            $arrayData['houseNumber']       = $regiCheckRslts->getHouseNumber();
            $arrayData['houseNumberSuffix'] = $regiCheckRslts->getHouseNumberSuffix();
            $arrayData['zipCode']           = $regiCheckRslts->getZipCode();
            $arrayData['city']              = $regiCheckRslts->getCity();
            $arrayData['countryCode']       = $regiCheckRslts->getCountryCode();
            $arrayData['phoneNumber']       = $regiCheckRslts->getPhoneNumber();
            $arrayData['corporationName']   = $regiCheckRslts->getCorporationName();
            $arrayData['cocNumber']         = $coc_number;
            $arrayData['streetName']        = $regiCheckRslts->getStreetName();

            $jsonData = json_encode($arrayData);

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody($jsonData);
        } catch (Exception $e) {
            Mage::log('Exception when calling registrationCheckApi->registrationCheckV2Post: '. $e->getMessage(), null, $this->_logfile);
        }

	}


	public function addresssplitterAction() {

		$address    = $this->getRequest()->getParam('address');
		$address2   = $this->getRequest()->getParam('address2');
		$address3   = $this->getRequest()->getParam('address3');

		$result     = array();

		if (isset($address2) && $address2 != '' && isset($address3) && $address3 != '') {
			
			$result['streetName']       = $address;
			$result['houseNumber']      = $address2;
			$result['houseNumberSuffix'] = $address3;

		} else if (isset($address2) && $address2 != '') {
			
			$result['streetName'] = $address;

			// Pregmatch pattern, dutch addresses
			$pattern = '#^([0-9]{1,5})([a-z0-9 \-/]{0,})$#i';

			preg_match($pattern, $address2, $houseNumbers);

			$result['houseNumber'] = $houseNumbers[1];
			$result['houseNumberSuffix'] = (isset($houseNumbers[2])) ? $houseNumbers[2] : '';

		} else {

			// Pregmatch pattern, dutch addresses
			$pattern = '#^([a-z0-9 [:punct:]\']*) ([0-9]{1,5})([a-z0-9 \-/]{0,})$#i';

            // you do not know if you have an address here! So please check first
            // otherwise logging is filled with errors such as
            // Undefined offset: 2 in .../Tritac/Capayable/controllers/AjaxController.php on line 77
            if(strlen($address) > 0) {
                preg_match($pattern, $address, $addressParts);

                if(count($addressParts) > 1) {
                    $result['streetName'] = $addressParts[1];
                    $result['houseNumber'] = $addressParts[2];
                    $result['houseNumberSuffix'] = (isset($addressParts[3])) ? $addressParts[3] : '';
                }
            }
		}

		$jsonData = json_encode($result);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);
    }
}