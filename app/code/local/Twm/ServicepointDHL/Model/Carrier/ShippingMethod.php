<?php

class Twm_ServicepointDHL_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'servicepointdhl';

    public function getDHLAddress($code) {
        $key = $this->_code . $code;
        $cached = Mage::app()->getCache();
        if (($result = $cached->load($key)) !== false) {
            $result = Zend_Json::decode($result);
            //return $result;
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

    public function getDHLAddresses($query) {
        $key = strtolower($this->_code . $query);
        $cached = Mage::app()->getCache();
        if (($result = $cached->load($key)) !== false) {
            $result = Zend_Json::decode($result);
            $result = array_slice($result['data']['items'], 0, 3);
            return $result;
        }

        $uri = 'https://dhlforyounl-dhlforyou-service-point-locator-benelux-v1.p.mashape.com/datamoduleAPI.jsp?action=public.splist&country_from=NL&country_results=NL&ot=n&v=2&s='.urlencode($query);
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
        $requestDhl = clone $request;
        $origCity = $requestDhl->getOrigCity();
        $origPostcode = $requestDhl->getOrigPostcode();

        $result = Mage::getModel('servicepointdhl/rate_result');

        $price = Mage::getStoreConfig("carriers/{$this->_code}/price");
        $carrierTitle = Mage::getStoreConfig("carriers/{$this->_code}/title");

        $request = Mage::app()->getRequest();

        //$searchCity = $request->getParam('servicepointdhl_city');
        $searchId = $request->getParam('servicepointdhl_id');
        if (empty($searchId)) {
            $searchId = Mage::getSingleton('checkout/session')->getTmpDhlId();
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if (!empty($searchId)) {
            $carrier = $this->getDHLAddress($searchId);
            if ($carrier) {
                Mage::getSingleton('checkout/session')->setTmpDhlId($searchId);
                $method = Mage::getModel('shipping/rate_result_method');

                $_address = '<br/>';
                $_address .= $carrier['add'] . '<br/>';
                $_address .= $carrier['zip'] . ' ' . $carrier['city'] . '<br/>';
                //$_address .= '<a href="#" data-toggle="popover" data-placement="top" data-html="true" data-trigger="focus" title="Openingstijden" data-content="'.$carrier['d_opening'].'">Openingstijden</a>';

                $method->setCarrier($this->_code);
                $method->setCarrierTitle($carrierTitle);

                $method->setMethod($carrier['psid']);
                $method->setMethodTitle($carrier['name'] . $_address);

                $method->setCost($price);
                $method->setPrice($price);

                $result->append($method);
                //return $result;
            }
		}

        $postcode = Mage::getSingleton('checkout/session')->getTmpPostcode();
        $houseNumber = Mage::getSingleton('checkout/session')->getTmpHouseNumber();
        if (!empty($postcode) && !empty($houseNumber)) {
            $query = str_replace(' ', '', $postcode) . ' ' . $houseNumber;
        } else {
            $query = str_replace(' ', '', $quote->getShippingAddress()->getPostcode()) . ' ' . $quote->getShippingAddress()->getStreet2();
        }

        foreach ($this->getDHLAddresses($query) as $carrier) {
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
