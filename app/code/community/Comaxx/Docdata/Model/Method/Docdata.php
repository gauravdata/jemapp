<?php

/**
 * Payment method class for Docdata Webmenu
 */
class Comaxx_Docdata_Model_Method_Docdata extends Comaxx_Docdata_Model_Method_Abstract {
	protected $_code = 'docdata_payments';
	
	/**
	 * Specifies if the current payment method may be used for together with the selected billing country
	 *
	 * @param string $country 3char ISO country code
	 * @see Smile_Docdata_Model_Method_Abstract::canUseForCountry()
	 *
	 * @return void
	 */
	public function canUseForCountry($country) {
		// The webmenu is available in all countries
		return true;
	}
	
	/**
	 * Checks if user needs to select payment method before Docdata webmenu
	 *
	 * @return boolean True if user DOES NOT need to select payment method before Docdata webmenu otherwise false
	 */
	protected function checkDocdataWebmenu() {
		// Do we need to use the webmenu?
		return Mage::helper('docdata/config')->getItem('general/webmenu_active');
	}
	
	/**
	 * Retrieve payment method title
	 *
	 * @return string
	 */
	public function getTitle() {
		return Mage::helper('docdata/config')->getItem('general/docdata_payment_title');
	}
}