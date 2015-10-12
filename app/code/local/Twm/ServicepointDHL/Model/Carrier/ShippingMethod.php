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

        $uri = 'https://dhlforyounl-dhlforyounl-service-point-locator.p.mashape.com/datamoduleAPI.jsp?action=public.splist&country_from=NL&country_results=NL&ot=n&spid=' . urlencode($code) . '&v=2';
        Mage::log($uri);
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
            $result = array_slice($result['data']['items'], 0, 3);
            return $result;
        }

        $query = !empty($postcode) ? $postcode . ' ' . Mage::getModel('core/session')->getLookupHouseNumer() : $city;

        $uri = 'https://dhlforyounl-dhlforyounl-service-point-locator.p.mashape.com/datamoduleAPI.jsp?action=public.splist&country_from=NL&country_results=NL&ot=n&v=2&s=' . urlencode($query);
        Mage::log($uri);
        $client = new Zend_Http_Client($uri);
        $client->setHeaders(array(
            'X-Mashape-Key' => '2mkhycZJq1msh6dAfgbllXxrSr5Wp1rotFHjsnEknupB8oHZcD',
            'Accept' => 'application/json'
        ));

        $response = $client->request();
        if ($response->isSuccessful()) {
            $result = Zend_Json::decode($response->getBody());

	    if (isset($result['status_msg'])) {
		//Mage::getSingleton('core/session')->addNotice($result['status_msg']);	
	    }
            $cached->save($response->getBody(), $key, array("dhl"), 30 * 24 * 3600);
            $result = array_slice($result['data']['items'], 0, 3);
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
            $postcode = $quote->getShippingAddress()->getPostcode(); // . ' ' . $quote->getShippingAddress()->getStreet1();
            $city = $quote->getShippingAddress()->getCity();
		}

        $result = Mage::getModel('servicepointdhl/rate_result');

        $price = Mage::getStoreConfig("carriers/{$this->_code}/price");
        $carrierTitle = Mage::getStoreConfig("carriers/{$this->_code}/title");

        foreach ($this->getDHLAddresses($postcode, $city) as $carrier) {
            $method = Mage::getModel('shipping/rate_result_method');

            $_address = '<br/>';
            $_address .= $carrier['add'] . '<br/>';
            $_address .= $carrier['zip'] .' ' . $carrier['city'] . '<br/>';
            //$_address .= '<a href="#" data-toggle="popover" data-placement="top" data-html="true" data-trigger="focus" title="Openingstijden" data-content="'.$carrier['d_opening'].'">Openingstijden</a>';

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($carrierTitle);

            $method->setMethod($carrier['psid']);
            $method->setMethodTitle($carrier['name'] . $_address);

            $method->setCost($price);
            $method->setPrice($price);

            $result->append($method);
        }

		return $result;
	 }

 }
