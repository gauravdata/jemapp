<?php
/**
 * Abstract class for api models
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
abstract class Comaxx_Docdata_Model_Api_Abstract {
	
	const SEVERITY_DEBUG 	= 'debug',
		  SEVERITY_INFO		= 'information',
		  SEVERITY_WARN 	= 'warning',
		  SEVERITY_ERROR 	= 'error';
	
	
	/**
	 * Docdata API method. Send a message request to Docdata
	 *
	 * @param Comaxx_Docdata_Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Comaxx_Docdata_Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Comaxx_Docdata_Model_Api_Response_Abstract
	 */
	abstract public function call(Comaxx_Docdata_Model_System $api, Comaxx_Docdata_Model_Api_Response $response_object, array $elements);
	
	/**
	 * Creates a soap connection
	 *
	 * $return 
	 */
	public function getConnection(Comaxx_Docdata_Model_System $api) {
		$uri = $api->getWsdlUri();
		
		$options = array('trace' => true,
				'keep_alive' => false);
		
		$encrypt = Mage::helper('docdata/config')->getItem('connection/soap_encryption');
		if ($encrypt) {
			$opts = array(
				'ssl' => array('ciphers'=>'RC4-SHA')
			);
			$options['stream_context'] = stream_context_create($opts);
		}
		
		//create new api connection to be certain it has the correct settings
		$connection = new SoapClient(
			$uri,
			$options
		);
		return $connection;
	}
}