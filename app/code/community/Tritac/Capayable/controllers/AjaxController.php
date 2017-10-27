<?php
class Tritac_Capayable_AjaxController extends Mage_Core_Controller_Front_Action{

	public function registrationcheckAction() {

		$helper     = Mage::helper('capayable');
		$public_key = $helper->getPublicKey();
		$secret_key = $helper->getSecretKey();
		$mode       = $helper->getMode();

		$client = new Tritac_CapayableApiClient_Client($public_key, $secret_key, $mode);

		$coc_number = $this->getRequest()->getParam("coc_number");
		if (!$coc_number) {
			$coc_number = 0;
		}

		$coc_number = intval($coc_number);

    	$registrationCheckRequest = new Tritac_CapayableApiClient_Models_RegistrationCheckRequest($coc_number);
		$registrationCheckResult = $client->doRegistrationCheck($registrationCheckRequest);

		$arrayData = array();
		$arrayData['isAccepted']        = $registrationCheckResult->getIsAccepted();
		$arrayData['houseNumber']       = $registrationCheckResult->getHouseNumber();
		$arrayData['houseNumberSuffix'] = $registrationCheckResult->getHouseNumberSuffix();
		$arrayData['zipCode']           = $registrationCheckResult->getZipCode();
		$arrayData['city']              = $registrationCheckResult->getCity();
		$arrayData['countryCode']       = $registrationCheckResult->getCountryCode();
		$arrayData['phoneNumber']       = $registrationCheckResult->getPhoneNumber();
		$arrayData['corporationName']   = $registrationCheckResult->getCorporationName();
		$arrayData['cocNumber']         = $coc_number;
		$arrayData['streetName']        = $registrationCheckResult->getStreetName();

		$jsonData = json_encode($arrayData);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);

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