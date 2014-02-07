<?php
/**
 * API helper class for create call
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Helper_Api_Create extends Comaxx_Docdata_Helper_Api_Abstract {
	const GUEST_SHOPPER_ID = 'GUEST',
		  GENDER_UNKNOWN = 'U',
		  DEFAULT_ENCODING = 'UTF-8',
		  DEFAULT_WEBMENU_LANGUAGE = 'en'; // ISO 639-1
	
	// match pattern (1, 12, 123, etc 1a, 1bv, 23ab, etc)
	//protected $pattern = "/([0-9]{1,})[\\s]{0,1}([a-z]{0,})/";
	
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
	
	/**
	 * Retrieves the configured merchant data
	 *
	 * @return array Merchant data
	 */
	public function getPaymentPreferences() {
		
		$cfg_helper = Mage::helper('docdata/config');
		$cfg_group = Comaxx_Docdata_Helper_Config::GROUP_PAYMENT_PREF;
		
		$result = array(
			'profile' => $cfg_helper->getItem('profile', $cfg_group),
			'numberOfDaysToPay' => $cfg_helper->getItem('number_of_days_to_pay', $cfg_group)
		);
		
		//add exhortation period 1 if configured
		$period_days = $cfg_helper->getItem('exhortation_period1_number_days', $cfg_group);
		$period_profile = $cfg_helper->getItem('exhortation_period1_profile', $cfg_group);
		
		if (!empty($period_days) && !empty($period_profile)) {
			$result['exhortation'] = array();
			$result['exhortation']['period1'] = array(
				'numberOfDays' => $period_days,
				'profile' => $period_profile
			);
		}
		
		//add exhortation period 2 if configured
		$period_days2 = $cfg_helper->getItem('exhortation_period2_number_days', $cfg_group);
		$period_profile2 = $cfg_helper->getItem('exhortation_period2_profile', $cfg_group);
		
		//in case there is no exhortation 1 the 2nd should not be sent either.
		if (!empty($period_days2) && !empty($period_profile2) && isset($result['exhortation'])) {
			$result['exhortation']['period2'] = array(
				'numberOfDays' => $period_days2,
				'profile' => $period_profile2
			);
		}
		
		return $result;
	}
	
	/**
	 * Retrieves the configured menu preference data
	 *
	 * @return array Preference data
	 */
	public function getMenuPreference() {
		$result = null;
		
		$css = Mage::helper('docdata/config')->getItem(
			'webmenu_css_id',
			Comaxx_Docdata_Helper_Config::GROUP_GENERAL
		);
		
		if (!empty($css)) {
			$result = array(
				'css' => array(
					'id' => $css
				)
			);
		}
		
		return $result;
	}
	
	/**
	 * Retrieves the customer gender
	 *
	 * @param string $gender Gender of the customer
	 * 
	 * @return string Docdata gender
	 */
	private function _getDocdataGender($gender = null) {
		$result;
		
		$options = Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getAllOptions();

		foreach ($options as $option) {
			if ($option['value'] == $gender) {
				if (!empty($option['label'])) {
					$result = strtoupper(substr($option['label'], 0, 1));
				} else {
					$result = self::GENDER_UNKNOWN;
				}
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Retrieves Magento store language
	 *
	 * @return string Magento language
	 */
	public function getLanguage() {
		$locale = Mage::app()->getLocale()->getLocaleCode();
		// locale is language_Region, only language part is needed
		$pos = strpos($locale, '_');
		if ($pos !== false) {
			$locale = Mage::helper('core/string')->substr($locale, 0, $pos);
		}
		// language must be supported by Docdata, the order will be cancelled if it is not...
		// We might want to make this option configurable per store instead of substituting
		// anything not recognized by english...
		$supported = array('nl','en','de','cz','da','es','fi','fr','hu','it','no','pl','pt','sv');
		if (!in_array($locale, $supported)) {
			$locale = self::DEFAULT_WEBMENU_LANGUAGE;
		}
		return $locale;
	}
	
	/**
	 * Retrieves Name data from user 
	 *
	 * @param mixed $user_data Object containing user data
	 *
	 * @return string Name data
	 */
	public function getName($user_data) {
		$firstnames = explode(' ', trim($user_data->getFirstname()));
		$initials = '';
		foreach($firstnames as $part) {
			if(!empty($part)) {
				$initials .= Mage::helper('core/string')->substr($part, 0, 1) . '.';
			}
		}
		
		return array(
			'initials' => $this->_limitLength($initials, 35), 					// Max 35 chars
			'first' => $this->_limitLength($user_data->getFirstname(), 35), 	// Max 35 chars
			'last' => $this->_limitLength($user_data->getLastname(), 35)		// Max 35 chars
		);
	}
	
	/**
	 * Retrieves the shopper data
	 *
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * 
	 * @return array Shopper data
	 */
	public function getShopper(Mage_Sales_Model_Order $order) {
		$id = $order->getCustomerId();
		$result = array();
		
		if ($id) {
			//gets Mage_Customer_Model_Customer
			$customer = Mage::getModel('customer/customer')->load($id);
			
			$result['id'] = $id;
			$result['name'] = $this->getName($customer);
			$result['email'] = $this->_validateEmail($customer->getEmail()); // Max 100 chars
			$result['gender'] = $this->_getDocdataGender($customer->getGender());
			
			$dob = $customer->getDob();
			if($dob !== null) {
				//extract only date section (time is not needed)
				$sections = explode(' ', $dob);
				$result['dateOfBirth'] = $sections[0];
			}
			$result['language'] = array('code' => $this->getLanguage());
			$result['shopper_phonenumber'] = $customer->getPrimaryBillingAddress()->getTelephone();
		} else {
			//gets Mage_Sales_Model_Order_Address
			$billingAddress = $order->getBillingAddress();
			
			$result['id'] = self::GUEST_SHOPPER_ID;
			$result['name'] = $this->getName($billingAddress);
			$result['email'] = $this->_validateEmail($billingAddress->getEmail()); // Max 100 chars
			$result['gender'] = $this->_getDocdataGender($order->getCustomerGender());
			
			$dob = $order->getCustomerDob();
			if($dob !== null) {
				//extract only date section (time is not needed)
				$sections = explode(' ', $dob);
				$result['dateOfBirth'] = $sections[0];
			}
			$result['language'] = array('code' => $this->getLanguage());
			$result['shopper_phonenumber'] = $billingAddress->getTelephone();
		}
		
		return $result;
	}
	
	/**
	 * Retrieves the total gross amount
	 *
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * 
	 * @return array Total gross amount data
	 */
	public function getTotalGrossAmount(Mage_Sales_Model_Order $order) {
		$currency = $order->getOrderCurrencyCode();
		$total_gross_amount = Mage::helper('docdata')->getAmountInMinorUnit($order->getGrandTotal(), $currency);
		
		return array('_' => $total_gross_amount, 'currency' => $currency);
	}
	
	/**
	 * Retrieves the total net amount
	 *
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * 
	 * @return array Total net amount data
	 */
	public function getTotalNetAmount(Mage_Sales_Model_Order $order) {
		$currency = $order->getOrderCurrencyCode();
		$total_gross_amount = Mage::helper('docdata')->getAmountInMinorUnit($order->getGrandTotal() - $order->getTaxAmount(), $currency);
		
		return array('_' => $total_gross_amount, 'currency' => $currency);
	}
		
	/**
	 * Retrieves address data from object
	 *
	 * @param Mage_Sales_Model_Order_Address $address Address object
	 * 
	 * @return array Address data
	 */
	private function _getAddressData(Mage_Sales_Model_Order_Address $address) {
		$result = array();
		
		$company = $address->getCompany();
		if ($company !== null) {
			$result['company'] = $this->_limitLength($company, 35); // Max 35 chars
		}
		
		// Try to get a docdata/afterpay specific address first
		$street = $address->getDocdataExtraStreet();
		if (!$street) { // Street should be set, if it isn't use the other data
			$street_full = $address->getStreetFull();
			$result['street'] = $this->_limitLength($this->_getStreetFromAddress($street_full), 35); // Max 35 chars
			$result['houseNumber'] = $this->_limitLength($this->_getStreetNumber($street_full), 35); // Max 35 chars
			$house_nr_add = $this->_getStreetNumberAddition($street_full);
			if (!empty($house_nr_add)) {
				$result['houseNumberAddition'] = $this->_limitLength($house_nr_add, 35); // Max 35 chars
			}
		} else {
			$houseNumber = $address->getDocdataExtraHousenumber();
			$houseNumberAdd = $address->getDocdataExtraHousenumberAddition();
			$result['street'] = $this->_limitLength($street, 35); // Max 35 chars
			$result['houseNumber'] = $this->_limitLength($houseNumber, 35); // Max 35 chars
			if (!empty($houseNumberAdd)) {
				$result['houseNumberAddition'] = $this->_limitLength($houseNumberAdd, 35); // Max 35 chars
			}
		}
		// suppress spaces in postal code
		$result['postalCode'] = str_replace(' ', '', $address->getPostcode()); // Min 1 character max 50, NMTOKEN
		$result['city'] = $this->_limitLength($address->getCity(), 35); // Max 35 chars
		$result['country'] = array('code' => $address->getCountryId());
		
		return $result;
	}
	
	/**
	 * Retrieves street number from full address
	 *
	 * @param string $street_full Full street 
	 * 
	 * @return mixed Streetnumber
	 */
	private function _getStreetNumber($street_full) {
		$numbers = $this->_getStreetNumberMatches($street_full);
		
		if (is_array($numbers) && count($numbers) > 0) {
			return intval($numbers[0]);
		}
		
		//Docdata requires housenumber, in case none is present add 0 indication.
		return '0';
	}
	
	/**
	 * Retrieves street number addition from full address
	 *
	 * @param string $street_full Full street 
	 * 
	 * @return string Streetnumber addition
	 */
	private function _getStreetNumberAddition($street_full) {
		$numbers = $this->_getStreetNumberMatches($street_full);
		
		if (is_array($numbers) && count($numbers) > 0) {
			return preg_replace('/^[0-9\\s-]+/', '', $numbers[0]);
		}
		
		return null;
	}
	
	/**
	 * Retrieves street from full address
	 *
	 * @param string $street_full Full street 
	 * 
	 * @return string Street
	 */
	private function _getStreetFromAddress($street_full) {
		if (is_array($street_full)) {
			
			$street_combined = array();
			//combine all adress lines
			foreach ($street_full as $street_line) {
				if (is_string($street_line)) {
					$street_combined[] = trim($street_line);
				}
			}
			
			if (empty($street_combined)) {
				//empty array found, return empty
				return '';
			} else {
				$street_full = implode(" ", $street_combined);
			}
		} elseif (!is_string($street_full)) {
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
			if ($match !== 'st' && $match !== 'nd' && $match !== 'rd' && $match !== 'th') {
				$final_matches[] = $number;
			}
		}
		
		return $final_matches;
	}
	
	/**
	 * Retrieves billing data
	 *
	 * @param Mage_Sales_Model_Order $order Order containing billing info
	 * 
	 * @return array Billing data
	 */
	public function getBillTo(Mage_Sales_Model_Order $order) {
		$billing_address = $order->getBillingAddress();
		return array(
			'name' => $this->getName($billing_address),
			'address' => $this->_getAddressData($billing_address)
		);
	}
	
	/**
	 * Retrieves total vat amount
	 *
	 * @param float $amount Amount of vat
	 * @param decimal $tax_percent Tax percentage
	 * @param string $currency Currency code
	 * 
	 * @return array Total vat amount
	 */
	private function _getTotalVatAmount($amount, $tax_percent, $currency) {
		$total_vat = Mage::helper('docdata')->getAmountInMinorUnit(
			$amount,
			$currency
		);
			
		return array(
			'_' => $total_vat,
			'rate' => $tax_percent,
			'currency' => $currency
		);
	}
	
	/**
	 * Retrieves invoice data
	 *
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * 
	 * @return array Invoice data
	 */
	public function getInvoiceData(Mage_Sales_Model_Order $order) {
		$billing_address = $order->getBillingAddress();
		
		$result = array(
			'totalNetAmount' => $this->getTotalNetAmount($order),
			'shipTo' => array(
				'name' => $this->getName($billing_address),
				'address' => $this->_getAddressData($billing_address)
			)
		);
		
		//item and totalVatAmount arrays for the invoice
		$items = array();
		$total_vat_amounts = array();
		$currency = $order->getOrderCurrencyCode();
		$country = $billing_address->getCountryId();
		
		//iterate through all items extracting totalVatAmount's and Item data
		foreach ($order->getAllItems() as $order_item) {
			//information is stored in the child items
			if ($order_item->getParentItemId() !== null) 
				continue;
			
			//total vat amount for current item
			$total_vat_amounts[] = $this->_getTotalVatAmount(
				$order_item->getTaxAmount(),
				$order_item->getTaxPercent(),
				$currency
			);
			
			//add item
			$items[] = $this->getOrderItem(count($items), $order_item, $currency, $country);
		}
		
		//add shipping as an item, start by getting shipping tax
		$shipping_tax_class = Mage::helper('tax')->getShippingTaxClass($order->getStore());
		$quote = Mage::getModel('checkout/cart')->getQuote();
		$cust_tax_class_id = $quote->getCustomerTaxClassId();
		$tax_calculation_model = Mage::getSingleton('tax/calculation');
		$request = $tax_calculation_model->getRateRequest(
			$quote->getShippingAddress(),
			$quote->getBillingAddress(),
			$cust_tax_class_id,
			$quote->getStore()
		);
		$tax_percent = ($shipping_tax_class)  
			? $tax_calculation_model->getRate($request->setProductClassId($shipping_tax_class))
			: 0;
		
		$total_vat_amounts[] = $this->_getTotalVatAmount(
			$order->getShippingTaxAmount(),
			$tax_percent,
			$currency
		);
		
		// Add order item for shipment cost if it is a shippable item (and not virtual)
		if ($order->canShip()) {
			$items[] = $this->getShippingItem(count($items), $order, $tax_percent, $currency, $country);
		}
		
		//add discount as item
		$discount = $order->getDiscountAmount();
		if (isset($discount) && $discount != 0) {
			$items[] = $this->getDiscountItem(count($items), $order, $currency, $country);
		}
		
		$result['item'] = $items;
		$result['totalVatAmount'] = $total_vat_amounts;
		
		return $result;
	}
	
	/**
	 * Retrieves Order item 
	 *
	 * @param int $number Item number
	 * @param Mage_Sales_Model_Order_Item $item Magento item 
	 * @param string $currency Currency code
	 * @param string $country Country code
	 * 
	 * @return array order item data
	 */
	public function getOrderItem($number, Mage_Sales_Model_Order_Item $item, $currency, $country) {
		
		$tax_percent = $item->getTaxPercent();
		$qty = $item->getQtyOrdered();
		$helper = Mage::helper('docdata');
		
		//get image url
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$image_url = $this->_sanitizeUrl($product->getImageUrl());
		if ($image_url === 'no_selection'
			|| strlen($image_url) > 250) { // Max 250 chars
			//no image present or too long url
			$image_url = null;
		}
		
		$item = array(
			'number' => $this->_limitLength($number, 50), // max 50 chars
			'name' => $this->_limitLength($item->getName(), 50), // max 50 chars
			'code' => $this->_limitLength($item->getSku(), 50), // max 50 chars
			'quantity' => array(
				'_' => $qty,
				'unitOfMeasure' => 'PCS'
			),
			'description' => $this->_limitLength($item->getName(), 100), // Max 100 chars
			'image' => $image_url, 
			'netAmount' => array(
				'_' => $helper->getAmountInMinorUnit($item->getPrice(), $currency),
				'currency' => $currency
			),
			'grossAmount' => array(
				'_' => $helper->getAmountInMinorUnit($item->getPriceInclTax(), $currency),
				'currency' => $currency
			),
			'vat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $helper->getAmountInMinorUnit($item->getTaxAmount() / $qty, $currency),
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
			'totalNetAmount' => array(
				'_' => $helper->getAmountInMinorUnit($item->getRowTotal(), $currency),
				'currency' => $currency
			),
			'totalGrossAmount' => array(
				'_' => $helper->getAmountInMinorUnit($item->getRowTotalInclTax(), $currency),
				'currency' => $currency
			),
			'totalVat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $helper->getAmountInMinorUnit($item->getTaxAmount(), $currency),
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
		);
		
		return $item;
	}
	
	/**
	 * Retrieves shipping item 
	 *
	 * @param int $number Item number
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * @param int $tax_percent Tax percentage
	 * @param string $currency Currency code
	 * @param string $country Country code
	 * 
	 * @return array order item data
	 */
	public function getShippingItem($number, Mage_Sales_Model_Order $order, $tax_percent, $currency, $country) {
		
		$tax = $order->getShippingTaxAmount();
		$amount = $order->getShippingAmount();
		$helper = Mage::helper('docdata');
		
		return array(
			'number' => $number,
			'name' => $this->_limitLength($this->__('Shipping'), 50), // Max 50 chars
			'code' => $this->_limitLength($this->__('Shipping'), 50), // Max 50 chars
			'quantity' => array(
				'_' => 1,
				'unitOfMeasure' => 'PCS'
			),
			'description' => $this->_limitLength($this->__("Shipping cost"), 100), // Max 100 chars
			'netAmount' => array(
				'_' => $helper->getAmountInMinorUnit($amount, $currency),
				'currency' => $currency
			),
			'grossAmount' => array(
				'_' => $helper->getAmountInMinorUnit($amount + $tax, $currency),
				'currency' => $currency
			),
			'vat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $helper->getAmountInMinorUnit($tax, $currency),
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
			'totalNetAmount' => array(
				'_' => $helper->getAmountInMinorUnit($amount, $currency),
				'currency' => $currency
			),
			'totalGrossAmount' => array(
				'_' => $helper->getAmountInMinorUnit($amount + $tax, $currency),
				'currency' => $currency
			),
			'totalVat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $helper->getAmountInMinorUnit($tax, $currency),
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
		);
	}
	
	/**
	 * Retrieves discount item 
	 *
	 * @param int $number Item number
	 * @param Mage_Sales_Model_Order $order Order for which the shopper needs to be extracted
	 * @param string $currency Currency code
	 * @param string $country Country code
	 * 
	 * @return array order item data
	 */
	public function getDiscountItem($number, Mage_Sales_Model_Order $order, $currency, $country) {
		
		$amount = Mage::helper('docdata')->getAmountInMinorUnit($order->getDiscountAmount(), $currency);
		
		return array(
			'number' => $number,
			'name' => $this->_limitLength($this->__('Discount'), 50), // Max 50 chars
			'code' => $this->_limitLength($this->__('Discount'), 50), // Max 50 chars
			'quantity' => array(
				'_' => 1,
				'unitOfMeasure' => 'PCS'
			),
			'description' => $this->_limitLength($this->__('Discount'), 100), // Max 100 chars
			'netAmount' => array(
				'_' => $amount,
				'currency' => $currency
			),
			'grossAmount' => array(
				'_' => $amount,
				'currency' => $currency
			),
			'vat' => array(
				'rate' => 0,
				'amount' => array(
					'_' => 0,
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
			'totalNetAmount' => array(
				'_' => $amount,
				'currency' => $currency
			),
			'totalGrossAmount' => array(
				'_' => $amount,
				'currency' => $currency
			),
			'totalVat' => array(
				'rate' => 0,
				'amount' => array(
					'_' => 0,
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			)
		);
	}
	
	/**
	 * Tries to retrieve an encoding to use with the given string. The beatifull part is that
	 * mb_detect_encoding will always return something usable by other multibyte strings.
	 *
	 * @param string $string String to evaluate the encoding of
	 *
	 * @return string The encoding extracted for the given string
	 */
	private function _getEncoding($string) {
		$encoding = mb_detect_encoding($string);
		return $encoding ? $encoding : self::DEFAULT_ENCODING;
	}
	
	/**
	 * Limits the given string to the given amount of characters, based upon the encoding of the string
	 *
	 * @param string $string The string to truncate to $length characters
	 * @param int $length The amount of characters that may be present in $string
	 *
	 * @return string The resulting string after peforming a substring on the given string
	 */
	private function _limitLength($string, $length) {
		$encoding = $this->_getEncoding($string);
		if (mb_strlen($string, $encoding) > $length) {
			Mage::helper('docdata')->log("The string '$string' will be shortened to $length characters.", Zend_Log::DEBUG);
		}
		return mb_substr($string, 0, $length, $encoding);
	}
	
	/**
	 * Validates and returns a given email address, throws an exception if the email is not valid.
	 *
	 * @param string $email A suposed email address to be validated
	 *
	 * @return string The email address given or void in case the exception is also thrown
	 */
	private function _validateEmail($email) {
		// This pattern is the one defined in the XSD
		$pattern = '^[_a-zA-Z0-9\-\+\.]+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*(\.[a-zA-Z]+)$';
		if (!preg_match("/$pattern/", $email)) {
			throw new Comaxx_Docdata_Model_System_Exception(
				__('The email address you tried to use was not accepted by our payment gateway, please choose another while placing the order again.'),
				Comaxx_Docdata_Model_System_Exception::VALIDATION_EMAIL
			);
		}
		
		// Magento already validates on email length however, make sure API can handle it (in case of Magento overrides/edits). 
		if (mb_strlen($email, $this->_getEncoding($email)) > 100) {
			throw new Comaxx_Docdata_Model_System_Exception(
				__('The email address you tried to use is too long for our payment gateway, please select a shorter email address when placing the order again.'),
				Comaxx_Docdata_Model_System_Exception::VALIDATION_EMAIL
			);
		}
		
		return $email;
	}
	
	/**
	 * URL Encodes the path parths and reassembles the url
	 * 
	 * @param string $url Url to sanitize
	 *
	 * @return string Sanitized URL or null if not sane matches could be made
	 */
	private function _sanitizeUrl($url) {
		preg_match('~^(?P<domain>https?://[^/]*/)(?P<path>.*)~', $url, $matches);
		
		if(is_array($matches) && isset($matches['path']) && isset($matches['domain'])) {
			return $matches['domain'] . implode('/', array_map('urlencode', explode('/', $matches['path'])));
		} else {
			// If the url can't be parsed, then what good is it to try and use it?
			// If we return the new url at this point it'll fail for sure...
			return null;
		}
	}
}