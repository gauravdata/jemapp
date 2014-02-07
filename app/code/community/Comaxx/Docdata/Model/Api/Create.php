<?php
/**
 * Api class for createRequest (creating payment order)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Api_Create extends Comaxx_Docdata_Model_Api_Abstract {
	
	/**
	 * @var Comaxx_Docdata_Model_Magento Parent Api class
	 */
	private $_api;
	
	/**
	 * Docdata API method. Send a payment request to Docdata
	 *
	 * @param Comaxx_Docdata_Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Comaxx_Docdata_Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Comaxx_Docdata_Model_Api_Response_Create
	 */
	public function call(Comaxx_Docdata_Model_System $api, Comaxx_Docdata_Model_Api_Response $response_object, array $elements) {
		$this->_api = $api;
		$result = $response_object; 
		
		try {
			$this->_api->log('API call Create: ', self::SEVERITY_DEBUG);
			$this->_api->log($elements, self::SEVERITY_DEBUG);
			//perform create call (wrap elements in array as rootelement)
			$client = $this->getConnection($api);
			$client->__soapCall('create', array($elements));
			
			$result->setResponse(
				$client->__getLastResponse()
			);
		} catch(Exception $ex) {
			$result->setErrorResponse($ex->getMessage());
		}
		
		if ($result->hasError()) {
			//log error 
			$api->log($result->getErrorMessage(), self::SEVERITY_ERROR);
		} else {
			// save the payment order key
			$node = $result->getNode('key');
			//only 1 result so get first and extract value by converting to string (simplexmlelement)
			$payment_order_key = (string)$node[0];
			$api->setDocdataPaymentOrderKey($payment_order_key);
			$api->log('A payment order has been created with the key: '.$payment_order_key);

			$api->setOrderStatus(
				array(
					$api::STATUS_NEW => $api->translate("New order payment created at Docdata.")
				)
			);
		}
		
		return $result;
	}
}