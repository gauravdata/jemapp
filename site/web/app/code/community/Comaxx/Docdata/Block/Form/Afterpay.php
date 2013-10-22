<?php
/**
 * Afterpay block
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Form_Afterpay extends Mage_Payment_Block_Form {
	protected $quote;
	
	/**
	 * @var string Matches possible house numbers + additions
	 *
	 * When applied to the following example:
	 * Derpaderpastreet 1, 12, 123, 1a, 1bv, 23ab, 1-a, 1-bv, 23-ab Derpaderpastreet
	 *
	 * Will match the numbers + letter additions (with dash) while leaving out
	 * the street. This should cover most international street formats, even when
	 * house numbers may be placed in front of the street.
	 */
	protected $pattern = "/\b(?P<numbers>[0-9]+[\\s-]*[a-z,0-9]{0,2}\\b)\b/i";
	
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('comaxx_docdata/form/afterpay.phtml');
		$this->quote = Mage::getSingleton('checkout/session')->getQuote();
	}
	
	/**
	 * Return true if the checkout is done as a guest
	 * 
	 * @return bool
	 */
	public function isGuestCheckout() {
		return !$this->quote->getCustomerId();
	}
	
	/**
	 * Return true if the billing and shipping addresses are the same
	 * 
	 * @return bool
	 */
	public function isBillingShippingSame() {
		return $this->quote->getShippingAddress()->getSameAsBilling();
	}
	
	/**
	 * Returns the customers phone number
	 * 
	 * @return string
	 */
	public function getCustomerPhone() {
		return $this->quote->getBillingAddress()->getTelephone();
	}
	
	 /**
	 * Returns the first character of the firstname, followed by a '.'.
	 * It's some sort of workaround because Afterpay needs initials, which is not available for a customer by default.
	 *
	 * @return string 
	 */
	public function getCustomerInitials() {
		return  Mage::helper('core/string')->substr($this->quote->getBillingAddress()->getFirstname(), 0, 1);
	}
	
	/**
	 * Returns the first character of the firstname, followed by a '.'.
	 * It's some sort of workaround because Afterpay needs initials, which is not available for a customer by default.
	 *
	 * @return string 
	 */
	public function getShippingInitials() {
		return Mage::helper('core/string')->substr($this->quote->getShippingAddress()->getFirstname(), 0, 1);
	}
	
		/**
	 * Gets the possible street number matches excluding common stuff which might also
	 * be present in a street name (1st, 2nd etc).
	 * 
	 * @param string $street_full Full street name
	 *
	 * @return array Array with matches in the given string
	 */
	private function _getStreetNumberMatches($street_full) {
		preg_match_all($this->pattern, $street_full, $matches, PREG_SET_ORDER);
		
		$final_matches = array();
		// Filter out stuff like
		// 1st, 2nd, 3rd, 4th
		foreach ($matches as $number_entry) {
			$number = $number_entry['numbers'];
			$match = substr(trim($number), -2);
			if($match !== 'st' && $match !== 'nd' && $match !== 'rd' && $match !== 'th') {
				$final_matches[] = $number;
			}
		}
		
		return $final_matches;
	}
	
	/**
	 * Gets the combined street name from an array
	 * 
	 * @param string $street_full Full street name
	 *
	 * @return string Street name if $street_full was an array, otherwise returns original parameter $street_full
	 */
	private function _getFullStreet($street_full) {
		if(is_array($street_full)) {
			
			$street_combined = array();
			//combine all adress lines
			foreach($street_full as $street_line) {
				if(is_string($street_line)) {
					$street_combined[] = trim($street_line);
				}
			}
			
			if(empty($street_combined)) {
				//empty array found, return empty
				return null;
			} else {
				$street_full = implode(" ", $street_combined);
				
			}
		}
		
		return $street_full;
	}
	
	/**
	 * Gets just the name in the street+number magento stores
	 * 
	 * @param string $street_full Full street name
	 *
	 * @return string Street name
	 */
	private function _getStreetName($street_full) {
		$street_full = $this->_getFullStreet($street_full);
		
		if (!is_string($street_full)) {
			return $street_full;
		}
		
		$numbers = $this->_getStreetNumberMatches($street_full);
		
		// From here we figure out what is just the street name using the matches
		foreach ($numbers as $number) {
			// Filter out only the numbers taking into account boundries, and dot/commas after it (which define a boundry)
			$street_full = preg_replace('/\b'.$number.'\b[,\.]?/i', '', $street_full);
		}
		
		return $street_full;
	}
	
	/**
	 * Tries to get the street number, if an invalid value is passed it will return false
	 * 
	 * @param string $street_full Full street name
	 *
	 * @return mixed false if no match could be made or the first viable number existing in the given string
	 */
	private function _getStreetNumber($street_full) {
		$street_full = $this->_getFullStreet($street_full);
		
		if (!is_string($street_full)) {
			return $street_full;
		}
		
		$matches = $this->_getStreetNumberMatches($street_full);
		
		if(count($matches) > 0) {
			// First viable number is used, this may be wrong but it is impossible to make a better match
			return intval($matches[0]);
		} else {
			return false;
		}
	}
	
	/**
	 * Tries to get an addition from the house number if it cannot make a match it will return false
	 * 
	 * @param string $street_full Full street name
	 *
	 * @return mixed false if no match could be made or the addition part from the first viable number in the given string
	 */
	private function _getStreetNumberAddition($street_full) {
		$street_full = $this->_getFullStreet($street_full);
		
		if (!is_string($street_full)) {
			return $street_full;
		}
		
		$matches = $this->_getStreetNumberMatches($street_full);
		
		if(count($matches) > 0) {
			// First viable number is used, this may be wrong but it is impossible to make a better match
			return preg_replace('/^[0-9\\s-]+/', '', $matches[0]);
		} else {
			return false;
		}
	}
	
	/**
	 * Get the street from the billing (customer) street address
	 *
	 * @return string Will always return a string, possibly without the house number which we try to filter out
	 */
	public function getCustomerStreet() {
		return $this->_getStreetName($this->quote->getBillingAddress()->getStreet());
	}
	
	/**
	 * Get the house number from the billing (customer) street address
	 *
	 * @return mixed false if no match can be made or a possible number
	 */
	public function getCustomerHousenumber() {
		return $this->_getStreetNumber($this->quote->getBillingAddress()->getStreet());
	}
	
	/**
	 * Get the addition to a house number if it exists from the billing (customer) address
	 *
	 * @return mixed false if no match can be made or the addition string
	 */
	public function getCustomerHousenumberAddition() {
		return $this->_getStreetNumberAddition($this->quote->getBillingAddress()->getStreet());
	}
	
	/**
	 * Get the street from the shipping (delivery) street address
	 *
	 * @return string Will always return a string, possibly without the house number which we try to filter out
	 */
	public function getShippingStreet() {
		return $this->_getStreetName($this->quote->getShippingAddress()->getStreet());
	}
		
	/**
	 * Get the house number from the shipping (delivery) street address
	 *
	 * @return mixed false if no match can be made or a possible number
	 */
	public function getShippingHousenumber() {
		return $this->_getStreetNumber($this->quote->getShippingAddress()->getStreet());
	}
	
	/**
	 * Get the addition to a house number if it exists from the shipping (delivery) street address
	 *
	 * @return mixed false if no match can be made or the addition string
	 */
	public function getShippingHousenumberAddition() {
		return $this->_getStreetNumberAddition($this->quote->getShippingAddress()->getStreet());
	}
}