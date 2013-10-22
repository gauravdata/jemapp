<?php
/**
 * Api class for refundRequest (refunding payment order)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Api_Refund extends Comaxx_Docdata_Model_Api_Abstract {

	/**
	 * @var Comaxx_Docdata_Model_Magento Parent Api class
	 */
	private $_api;

	/**
	 * Docdata API method. Send a refund request to Docdata
	 *
	 * @param Comaxx_Docdata_Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Comaxx_Docdata_Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Comaxx_Docdata_Model_Api_Response_Refund
	 */
	public function call(Comaxx_Docdata_Model_System $api, Comaxx_Docdata_Model_Api_Response $response_object, array $elements) {
		
		$this->_api = $api;
		$result = $response_object; 
		
		try {
			$this->_api->log('API call Refund: ', self::SEVERITY_DEBUG);
			$this->_api->log($elements, self::SEVERITY_DEBUG);
			//perform refund call (wrap elements in array as rootelement)
			$client = $this->getConnection($api);
			$client->__soapCall('refund', array($elements));
			
			$result->setResponse(
				$client->__getLastResponse()
			);
		} catch(Exception $ex) {
			$result->setErrorResponse($ex->getMessage());
		}
		
		if ($result->hasError()) {
			//log error 
			$this->_api->log($result->getErrorMessage(), self::SEVERITY_ERROR);
		} else {
			//reund request was successfull, log result
			$this->_api->log('A refund request has succeeded for a payment order with the payment id: ' . $elements['paymentId']);
		}
		
		return $result;
	}
}