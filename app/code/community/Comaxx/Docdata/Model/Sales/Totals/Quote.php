<?php
/**
 * Model to calculate grand total using additional fees on frontend 
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Totals_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	public function __construct() {
		//set code for this totals entry
		$this->setCode('docdata_payment_fee');
	}
	
	/**
	 * Collect function called by Magento. Includes afterpay costs into quote
	 *
	 * @param Mage_Sales_Model_Quote_Address $address address used to determine quote costs
	 *
	 * @return Mage_Sales_Model_Quote_Address_Total_Tax instance of current class
	 */
	public function collect(Mage_Sales_Model_Quote_Address $address) {
		parent::collect($address);
		
		$quote = $address->getQuote();
		$store = $quote->getStore();

		// check if afterpay is selected to add extra costs
		if (count($quote->getPaymentsCollection())) {
			$payment = $quote->getPayment();
			$method = null;
			
			if($payment->getMethod () !== null) {
				$method = $payment->getMethodInstance();
				$pm_code = $method->getCode();
			}
			
			if (!($method instanceof Comaxx_Docdata_Model_Method_Fee)) {
				//set additional costs to default
				$quote->setDocdataFeeAmount(0);
				$quote->setDocdataFeeTaxAmount(0);
				return $this;
			}
		} else {
			return $this;
		}
		
		$config_helper = Mage::helper('docdata/config');
		
		// check if there are extra costs
		$extra_costs = $config_helper->getPaymentMethodItem($pm_code, 'extra_costs');
		if(isset($extra_costs) && $extra_costs > 0) {
			// calculating only for billing address!
			if ($address->getAddressType() == 'billing') {
				//determine costs and include into address/quote
				$this->_includeAfterpayCosts($address, $quote, $extra_costs, $store, $config_helper);
			}
		} 
		return $this;
	}
	
	/**
	 * Calculates the cost details and inserts afterpay costs/tax in grand total/tax of quote 
	 *
	 * @param Mage_Sales_Model_Quote_Address $address address used to determine quote costs
	 * @param Mage_Sales_Model_Quote $quote quote that is to be updated
	 * @param string $extra_costs additional costs for this quote
	 * @param Mage_Core_Model_Store $store store this quote is created on
	 * @param Comaxx_Docdata_Helper_Config $config_helper helper for config values
	 *
	 * @return void
	 */
	private function _includeAfterpayCosts(Mage_Sales_Model_Quote_Address $address, Mage_Sales_Model_Quote $quote, $extra_costs, Mage_Core_Model_Store $store, Comaxx_Docdata_Helper_Config $config_helper) {
		$cost = $extra_costs;
		$tax = 0;
		// initial tax calculation 
		$tax_calc = Mage::getSingleton('tax/calculation');
		$customer_tax_class = $quote->getCustomerTaxClassId();
		$afterpay_tax_class = $config_helper->getPaymentMethodItem($this->_pm_code, 'extra_costs_taxclass');
		$tax_included =  $config_helper->getPaymentMethodItem($this->_pm_code, 'extra_costs_tax_included');
		$request = $tax_calc->getRateRequest($address, $quote->getBillingAddress(), $customer_tax_class, $store);
		$rate = 0;
		
		// is tax already included
		if(isset($tax_included) && !$tax_included) {
			//tax is not included
			if ($afterpay_tax_class) {
				//use tax class to determine tax amount
				if ($rate = $tax_calc->getRate($request->setProductClassId($afterpay_tax_class))) {
					$tax = $cost * $rate / 100;
				} else {
					$tax = ($extra_costs / (100 + $rate)) * $rate;
					$cost = $extra_costs - $tax;
				}
			} 
		} else {
			// tax is included
			// calculate price excl tax 
			if ($afterpay_tax_class) {
				if ($rate = $tax_calc->getRate($request->setProductClassId($afterpay_tax_class))) {
					$tax = $cost - ($cost / ($rate + 100) * 100);
				} 
			}
			// deduct tax from basecost 
			$cost = $cost - $tax;
		}
		
		$cost = $store->roundPrice($cost);
		$tax = $store->roundPrice($tax);
		
		//convert currency of quote to store currency
		$cost = $store->convertPrice($cost, false);
		$tax = $store->convertPrice($tax, false);
		
		// Set new values for AfterPay cost to Address
		$quote->setDocdataFeeAmount($cost);
		$quote->setDocdataFeeTaxAmount($tax);
	}
	
	/**
	 * Used by Magento to extract a totals row for afterpay fee.
	 *
	 * @param Mage_Sales_Model_Quote_Address $address address used to determine quote costs
	 *
	 * @return 
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address) {
		$this->_setAddress($address);
		$quote = $address->getQuote();
		
		$cost = $quote->getDocdataFeeAmount();
		if ($cost > 0 && $address->getAddressType() === 'billing') {
			
			$address->addTotal(array(
				'code' => $this->getCode(),
				'title' => Mage::helper('docdata')->__('AfterPay servicekosten'),
				'value' => $cost
			));
		}

		return $this;
	}    
}