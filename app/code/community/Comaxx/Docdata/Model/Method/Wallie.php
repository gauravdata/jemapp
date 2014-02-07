<?php

/**
 * Payment method class for Wallie
 */
class Comaxx_Docdata_Model_Method_Wallie extends Comaxx_Docdata_Model_Method_Abstract {
	protected $_code = 'docdata_wlie';
	
	/**
	 * Return true if the payment method can be used in the checkout
	 *
	 * @param Mage_Sales_Model_Quote $quote Quote belonging to current checkout
	 * 
	 * @see Mage_Payment_Model_Method_Abstract::isAvailable()
	 *
	 * @return boolean True if the payment method can be used in the checkout, otherwise false
	 */
	public function isAvailable($quote = null) {
		//method is no longer available but remains in code to support viewing old orders made by this payment method.
		return false;
	}
}