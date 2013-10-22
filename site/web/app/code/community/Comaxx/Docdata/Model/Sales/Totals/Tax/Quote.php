<?php
/**
 * Model to calculate grand total using additional tax fees on frontend 
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Totals_Tax_Quote extends Mage_Sales_Model_Quote_Address_Total_Tax
{
	private $_pm_code;
	
	/**
	 * Collect function called by Magento. Includes costs/tax to quote.
	 *
	 * @param Mage_Sales_Model_Quote_Address $address address used to determine quote costs
	 *
	 * @return Mage_Sales_Model_Quote_Address_Total_Tax instance of current class
	 */
	public function collect(Mage_Sales_Model_Quote_Address $address) {
		$pm_code = null;
		$quote = $address->getQuote();
		$store = $quote->getStore();

		// check if afterpay is selected to add extra costs
		if (count($quote->getPaymentsCollection())) {
			$payment = $quote->getPayment();
			$method = null;
			
			if($payment->getMethod () !== null) {
				$method = $payment->getMethodInstance();
				$this->_pm_code = $pm_code = $method->getCode();
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
		
		//used to execute via shipping only (so it is only applied once and not on subtotal of order items)
		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}
		
		$config_helper = Mage::helper('docdata/config');
		
		// check if there are extra costs
		$extra_costs = $config_helper->getPaymentMethodItem($pm_code, 'extra_costs');
		
		if(isset($extra_costs) && $extra_costs > 0) {
			//determine costs and include into address/quote
			$this->_includePaymentCosts($address, $quote, $extra_costs, $store, $config_helper);
		} 
		return $this;
	}
	
	/**
	 * Calculates the cost details and inserts afterpay costs into quote (does not change Magento fields in quote)
	 *
	 * @param Mage_Sales_Model_Quote_Address $address address used to determine quote costs
	 * @param Mage_Sales_Model_Quote $quote quote that is to be updated
	 * @param string $extra_costs additional costs for this quote
	 * @param Mage_Core_Model_Store $store store this quote is created on
	 * @param Comaxx_Docdata_Helper_Config $config_helper helper for config values
	 *
	 * @return void
	 */
	private function _includePaymentCosts(Mage_Sales_Model_Quote_Address $address, Mage_Sales_Model_Quote $quote, $extra_costs, Mage_Core_Model_Store $store, Comaxx_Docdata_Helper_Config $config_helper) {
		$cost = $extra_costs;
		$tax = 0;
		// initial tax calculation 
		$tax_calc = Mage::getSingleton('tax/calculation');
		$customer_tax_class = $quote->getCustomerTaxClassId();
		$payment_tax_class = $config_helper->getPaymentMethodItem($this->_pm_code, 'extra_costs_taxclass');
		$tax_included =  $config_helper->getPaymentMethodItem($this->_pm_code, 'extra_costs_tax_included');
		$request = $tax_calc->getRateRequest($address, $quote->getBillingAddress(), $customer_tax_class, $store);
		$rate = 0;
		
		// is tax already included
		if(isset($tax_included) && !$tax_included) {
			//tax is not included
			if ($payment_tax_class) {
				//use tax class to determine tax amount
				if ($rate = $tax_calc->getRate($request->setProductClassId($payment_tax_class))) {
					$tax = $cost * $rate / 100;
					$cost += $tax;
				}
			} 
		} else {
			// tax is included
			// calculate price excl tax 
			if ($afterpay_tax_class) {
				if ($rate = $tax_calc->getRate($request->setProductClassId($payment_tax_class))) {
					$tax = $cost - ($cost / ($rate + 100) * 100);
				} 
			}
		}
		
		//set amounts in address
		$address->setTaxAmount($address->getTaxAmount() + $tax);
		$address->setBaseTaxAmount($address->getBaseTaxAmount() + $tax);

		$address->setGrandTotal($address->getGrandTotal() + $cost);
		$address->setBaseGrandTotal($address->getBaseGrandTotal() + $cost);
		
		//save tax info that was applied
		$this->_saveAppliedTaxes(
					$address,
					$tax_calc->getAppliedRates($request),
					$tax,
					$tax,
					$rate
				);
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
		
		//no need to add seperate line for tax, it is included in the main tax entry.
		return $this;
	}    
}