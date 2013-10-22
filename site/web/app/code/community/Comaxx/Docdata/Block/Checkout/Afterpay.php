<?php
class Comaxx_Docdata_Block_Checkout_Afterpay extends Mage_Checkout_Block_Total_Default {
	
	protected $_template = 'comaxx_docdata/checkout/fee.phtml';
	private $_tax_display,
			$_code = 'docdata_ap';
			
	const INCL_EXCL_TAX = '3',
		  INCL_TAX = '2',
		  EXCL_TAX = '1';
	
	public function __construct() {
		 $this->setTemplate($this->_template);
		 
		 $this->_tax_display = $this->helper('docdata/config')->getPaymentMethodItem($this->_code, 'extra_costs_displaytax');
	}
	
	/**
	 * Check if we need display afterpay costs include and exlude tax
	 *
	 * @return bool
	 */
	public function displayBoth() {
		return $this->_tax_display === self::INCL_EXCL_TAX;
	}
	
	/**
	 * Check if we need display shipping include tax
	 *
	 * @return bool
	 */
	public function displayIncludeTax() {
		return $this->_tax_display === self::INCL_TAX; 
	}
	
	/**
	 * Check if we need display shipping exclude tax
	 *
	 * @return bool
	 */
	public function displayExcludeTax() {
		return $this->_tax_display === self::EXCL_TAX; 
	}

	/**
	 * Get shipping amount include tax
	 *
	 * @return float
	 */
	public function getPaymentsFeeIncludeTax() {
		$address = $this->getTotal()->getAddress();
		$quote = $address->getQuote();
		
		return $quote->getDocdataFeeAmount() + $quote->getDocdataFeeTaxAmount();
	}

	/**
	 * Get shipping amount exclude tax
	 *
	 * @return float
	 */
	public function getPaymentsFeeExcludeTax() {
		$address = $this->getTotal()->getAddress();
		$quote = $address->getQuote();
		
		return $quote->getDocdataFeeAmount();
	}
	
	public function getIncludeTaxLabel() {
		$address = $this->getTotal()->getAddress();
		$quote = $address->getQuote();
		$payment = $quote->getPayment();
		$payment_name = '';
		
		if($payment->getMethod () !== null) {
			$method = $payment->getMethodInstance();
			$payment_name = $method->getPmName().' ';
		}
		
		return $this->helper('docdata')->__($payment_name.'servicekosten (Incl.BTW)');
	}
	
	public function getExcludeTaxLabel() {
		$address = $this->getTotal()->getAddress();
		$quote = $address->getQuote();
		$payment = $quote->getPayment();
		$payment_name = '';
		
		if($payment->getMethod () !== null) {
			$method = $payment->getMethodInstance();
			$payment_name = $method->getPmName().' ';
		}
		
		return $this->helper('docdata')->__($payment_name.'servicekosten (Excl.BTW)');
	}    
}