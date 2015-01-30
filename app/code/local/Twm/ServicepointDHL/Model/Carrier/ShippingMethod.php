<?php

class Twm_ServicepointDHL_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'servicepointdhl';

    public function getDHLAddress($code) {
        $key = $this->_code . $code;
        $cached = Mage::app()->getCache();
        if (($result = $cached->load($key)) !== false) {
            $result = Zend_Json::decode($result);
            return $result;
        }

        $uri = 'https://dhlforyounl-dhlforyounl-service-point-locator.p.mashape.com/datamoduleAPI.jsp?action=public.splist&ot=n&spid=' . urlencode($code) . '&v=2';
        $client = new Zend_Http_Client($uri);
        $client->setHeaders(array(
            'X-Mashape-Key' => '2mkhycZJq1msh6dAfgbllXxrSr5Wp1rotFHjsnEknupB8oHZcD',
            'Accept' => 'application/json'
        ));

        $response = $client->request();

        if ($response->isSuccessful()) {
            $cached->save($response->getBody(), $key, array("dhl"), 30 * 24 * 3600);
            $result = Zend_Json::decode($response->getBody());

            return $result['data']['items'][0];
        }
        return false;
    }

    public function getDHLAddresses($postcode = '', $city = '') {

        $key = strtolower($this->_code . $postcode . $city);
        $cached = Mage::app()->getCache();
        if (($result = $cached->load($key)) !== false) {
            $result = Zend_Json::decode($result);
            $result = array_slice($result['data']['items'], 0, 6);
            return $result;
        }

        $query = !empty($postcode) ? $postcode : $city;

        $uri = 'https://dhlforyounl-dhlforyounl-service-point-locator.p.mashape.com/datamoduleAPI.jsp?action=public.splist&ot=n&v=2&s=' . urlencode($query);
        $client = new Zend_Http_Client($uri);
        $client->setHeaders(array(
            'X-Mashape-Key' => '2mkhycZJq1msh6dAfgbllXxrSr5Wp1rotFHjsnEknupB8oHZcD',
            'Accept' => 'application/json'
        ));

        $response = $client->request();

        if ($response->isSuccessful()) {
            $cached->save($response->getBody(), $key, array("dhl"), 30 * 24 * 3600);
            $result = Zend_Json::decode($response->getBody());

            $result = array_slice($result['data']['items'], 0, 6);
            return $result;
        }
        return array();
    }

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $request = Mage::app()->getRequest();

		$searchPostcode = $request->getParam('servicepointdhl_postcode');
        $searchCity = $request->getParam('servicepointdhl_city');
		
        $quote = Mage::getSingleton('checkout/session')->getQuote();

		if ($searchPostcode || $searchCity) {
            $postcode = $searchPostcode;
            $city = $searchCity;
		} else {
            $postcode = $quote->getShippingAddress()->getPostcode();
            $city = $quote->getShippingAddress()->getCity();
		}

        $result = Mage::getModel('servicepointdhl/rate_result');

        $price = Mage::getStoreConfig("carriers/{$this->_code}/price");
        $carrierTitle = Mage::getStoreConfig("carriers/{$this->_code}/title");

        foreach ($this->getDHLAddresses($postcode, $city) as $carrier) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($carrierTitle);

            $method->setMethod($carrier['psid']);
            $method->setMethodTitle($carrier['name']);

            $method->setCost($price);
            $method->setPrice($price);

            $result->append($method);
        }

		return $result;
	 }

 }
