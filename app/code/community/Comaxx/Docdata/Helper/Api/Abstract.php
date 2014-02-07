<?php
/**
 * Abstract helper class for api helpers
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Helper_Api_Abstract extends Mage_Core_Helper_Abstract {
	//const api version
	const API_VERSION = '1.0';
	
	/**
	 * Retrieves the current API version
	 *
	 * @return string API version
	 */
	public function getApiVersion() {
		return self::API_VERSION;
	}
	
	/**
	 * Retrieves the configured merchant data
	 * @param int $store_id contains store id if data should be extracted from store other then current store.
	 *
	 * @return array Merchant data
	 */
	public function getMerchantDetails($store_id = null) {
		$merchant_config = Mage::helper('docdata/config')->getMerchant($store_id);
		
		return array(
			'name' => $merchant_config['username'],
			'password' => $merchant_config['password']
		);
	}
	
}