<?php
/**
 * Helper class for reading config values
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Helper_Config extends Mage_Core_Helper_Abstract {
	//define constants for WSDL URLs and config groups
	const GROUP_GENERAL = 'general',
		  GROUP_MERCHANT = 'merchant',
		  GROUP_PAYMENT_PREF = 'payment_preferences',
		  GROUP_STATUS = 'statuses',
		  GROUP_FALLBACK_STATUS = 'fallback_statuses';
	
	private static $_productionOption = "production";
	private $_general_cfg,
			$_merchant_cfg,
			$_payment_pref_cfg,
			$_statuses_cfg,
			$_fallback_statuses_cfg,
			$_payment_method_map;
	
	/**
	 * Constructor used to load config groups
	 * 
	 * @return Comaxx_Docdata_Helper_Config object
	 */
	public function __construct()
	{
		$config = Mage::getStoreConfig('docdata');
		$this->_general_cfg         	= $config['general'];
		$this->_merchant_cfg        	= $config['merchant_account'];
		$this->_payment_pref_cfg    	= $config['payment_preferences'];
		$this->_statuses_cfg        	= $config['custom_statuses'];
		$this->_fallback_statuses_cfg 	= $config['fallback_statuses'];
		
		
		//map command to payment method link
		$this->_payment_method_map = array();
		$payments = Mage::getStoreConfig('payment');
		foreach($payments as $key => $value) {
			if(strstr($key, 'docdata')) {
				$command = isset($value['command']) ? $value['command'] : null;
				//only add entry if command is set
				if(isset($command) && $command !== '') {
					$this->_payment_method_map[$value['command']] = $key;
				}
			}
		}
	}

	/**
	 * Checks if the plugin is enabled
	 * 
	 * @return boolean true if plugin is enabled, otherwise false
	 */
	public function isActive() {
		return (!empty($this->_general_cfg['active']) && ($this->_general_cfg['active'] === '1'));
	}
	
	/**
	 * Checks if the plugin is configured to production mode
	 * 
	 * @return boolean true if plugin is in production mode, otherwise false
	 */
	public function isProduction() {
		return (!empty($this->_general_cfg['module_mode']) && ($this->_general_cfg['module_mode'] === $this::$_productionOption));
	}
	
	 
	 /**
	 * Retrieves the Merchant settings
	 * @param int $store_id contains store id if data should be extracted from store other then current store.
	 * 
	 * @return array containing the configured username and password 
	 */
	public function getMerchant($store_id = null) {
		$prefix = $this->_general_cfg['module_mode'] . '_';
		
		$merchant_config = $this->_merchant_cfg;
		//in case different store info is required, use other config data
		if($store_id !== null) {
			$merchant_config = Mage::getStoreConfig('docdata/merchant_account', $store_id);
		}
		
		//get the settings belonging to correct module mode
		return array(
			'username' => empty($merchant_config[$prefix . 'username']) ? null : $merchant_config[$prefix . 'username'],
			'password' => empty($merchant_config[$prefix . 'password']) ? null : $merchant_config[$prefix . 'password']
		);
	}

	/**
	 * Tries to retrieve the code of a payment method using a command which should be configured
	 *
	 * @param string $command The command to use for the query
	 *
	 * @return string Code of a payment method found using the command of the payment method
	 */
	public function getPaymentCodeByCommand($command) {
		
		$result = 'docdata_payments';
		//try to find command in mapped data
		if(isset($this->_payment_method_map[$command])) {
			$result = $this->_payment_method_map[$command];
		} 
		
		return $result;
	}
	
	/**
	 * Retrieves an item in the specified group 
	 *
	 * @param string $key   Configuration key for the desired item
	 * @param string $group Configuration group to find item in
	 * 
	 * @return object Returns object if found otherwise returns null
	 */
	public function getItem($key, $group = null) {
		
		$result;
		switch($group) {
			case Comaxx_Docdata_Helper_Config::GROUP_GENERAL:
				$result = empty($this->_general_cfg[$key]) ? null : $this->_general_cfg[$key];
				break;
			case Comaxx_Docdata_Helper_Config::GROUP_MERCHANT:
				$result = empty($this->_merchant_cfg[$key]) ? null : $this->_merchant_cfg[$key];
				break;
			case Comaxx_Docdata_Helper_Config::GROUP_PAYMENT_PREF:
				$result = empty($this->_payment_pref_cfg[$key]) ? null : $this->_payment_pref_cfg[$key];
				break;
			case Comaxx_Docdata_Helper_Config::GROUP_STATUS:
				$result = empty($this->_statuses_cfg[$key]) ? null : $this->_statuses_cfg[$key];
				
				if($result === null) {
					Mage::helper('docdata')->log('No status configured for key '.$key);
					$result = $this->getItem($key, Comaxx_Docdata_Helper_Config::GROUP_FALLBACK_STATUS);
				}
				
				break;
			case Comaxx_Docdata_Helper_Config::GROUP_FALLBACK_STATUS:
				$result = empty($this->_fallback_statuses_cfg[$key]) ? null : $this->_fallback_statuses_cfg[$key];
				break;
			case null:
				//no group specified, attempt to find in config using only key
				$result =  Mage::getStoreConfig('docdata/' . $key);
				break;
		} 
		
		return $result;
	}
	
	/**
	 * Retrieves configuration for the requested payment method
	 *
	 * @param string $payment_method Key for the payment method
	 * @param string $key            Optional key for an item within the payment method to be retrieved
	 * (leave empty for all items related to the payment method)
	 * 
	 * @return object In case $key is defined returns only the specific setting,
	 * if $key is empty the payment method group is returned. In case no match is made returns null.
	 */
	public function getPaymentMethodItem($payment_method, $key = null) {
		//build required path section
		$path = 'payment/' . $payment_method;
		
		//check if optional path section needs to be included
		if(!empty($key)) {
			$path .= '/' . $key;
		}
		
		return Mage::getStoreConfig($path);
	}
	
	/**
	 * Retrieves the WSDL of the API
	 *
	 * @return string WSDL uri of the API
	 */
	public function getWsdlUri() {
		$prefix = $this->_general_cfg['module_mode'] . '/';
		
		return $this->getItem($prefix.'wsdl');
	}
	
	/**
	 * Retrieves the Webmenu Uri
	 *
	 * @return string Webmenu uri 
	 */
	public function getWebmenuUri() {
		$prefix = $this->_general_cfg['module_mode'] . '/';
		
		return $this->getItem($prefix.'webmenu');
	}
}