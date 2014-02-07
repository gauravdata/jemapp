<?php
/**
 * Generic helper class with function relevant to a wider scope of classes withing the Docdata plugin.
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Helper_Data extends Mage_Core_Helper_Abstract {
	
	private $_currency_helper;
	
	/**
	 * Add prefix to array keys
	 *
	 * @param string $prefix Prefix to add
	 * @param array $array Array to add the prefix to the first level keys
	 *
	 * @return array
	 */
	public function addPrefix($prefix, $array) {
		$prefixedArray = array();
		foreach ($array as $name => $value) {
			$prefixedArray[$prefix.$name] = $value;
		}
		return $prefixedArray;
	}
	
	/**
	 * Remove prefix from array keys
	 *
	 * @param string $prefix Prefix to remove
	 * @param array $array Array to remove the prefix from the first level
	 *
	 * @return array
	 */
	public function removePrefix($prefix, $array) {
		$unprefixedArray = array();
		foreach ($array as $name => $value) {
			if (strpos($name, $prefix) === 0) {
				$newName = substr($name, strlen($prefix));
				$unprefixedArray[$newName] = $value;
			}
		}
		return $unprefixedArray;
	}
	
	/**
	 * Restores the qoute (cart) with the content of the last order
	 *
	 * @return void
	 */
	public function restoreLastQoute() {
		/* @var $session Mage_Checkout_Model_Session */
		$session = Mage::getSingleton('checkout/session');
		$lastQuoteId = $session->getLastQuoteId();
		$session->clear();
		$session->getQuote()->load($lastQuoteId)->setIsActive(1);
		
		/* @var $cart Mage_Checkout_Model_Cart */
		$cart = Mage::getSingleton("checkout/cart");
		if ($cart->getItemsCount()) {
			$cart->init();
			$cart->save();
		}
	}
	
	/**
	 * Log message into docdata log
	 *
	 * @param mixed $message message to log
	 * @param integer $severity Zend_Log severity level
	 * 
	 * @return void
	 */
	public function log($message, $severity = Zend_Log::INFO) {
		Mage::log($message, $severity, 'docdata.log');
	}

	/**
	 * Return a price in the minor unit of the given currency
	 *
	 * @param float $amount amount described in the given currency with decimals
	 * @param string $currency currency used
	 * @return int
	 */
	public function getAmountInMinorUnit($amount, $currency) {
		
		if($this->_currency_helper === null) {
			$this->_currency_helper = Mage::helper('docdata/currency');
		}
		
		//get currencies minorunit amount
		$minor_units = $this->_currency_helper->getMinorUnits($currency);
		
		return round($amount * pow(10, $minor_units));
	}
	
	/**
	 * Return a price in the major unit of the given currency
	 *
	 * @param float $amount amount described in the given currency without the decimels
	 * @param string $currency currency used
	 * @return int
	 */
	public function getAmountInMajorUnit($amount, $currency) {
		
		if($this->_currency_helper === null) {
			$this->_currency_helper = Mage::helper('docdata/currency');
		}
		
		//get currencies minorunit amount
		$major_units = $this->_currency_helper->getMinorUnits($currency);
		
		return $amount / pow(10, $major_units);
	}
}