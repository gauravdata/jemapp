<?php
/**
 * Model to calculate grand total using additional fees on frontend 
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Totals_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Subtotal {

	/**
	 * Collect invoice subtotal
	 *
	 * @param   Mage_Sales_Model_Order_Invoice $invoice
	 * @return  Mage_Sales_Model_Order_Invoice_Total_Subtotal
	 */
	public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
		$order = $invoice->getOrder();
		
		//  check if afterpay is selected to add extra costs
		if (count($order->getPaymentsCollection())) {
			$payment = $order->getPayment();
			$method = null;
			
			if($payment->getMethod () !== null) {
				$method = $payment->getMethodInstance();
			}
			
			if ($invoice->getDocdataFeeAmount() == 0 || $invoice->getDocdataFeeAmount() == null) {
				return $this;
			}
		} else {
			return $this;
		}
		
		//afterpay is used in this order, add extra cost
		//note: values are already on invoice object, just not included in total
		
		//update grand total with afterpay costs
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() +
									$invoice->getDocdataFeeAmount());
		$invoice->setGrandTotal($invoice->getGrandTotal() +
								$invoice->getDocdataFeeAmount());
		
		//subtotal(incl tax) is incl afterpay tax at the moment, remove it for proper totals overview
		$invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() -
									 $invoice->getDocdataFeeTaxAmount());
		$invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() -
										 $invoice->getDocdataFeeTaxAmount());
		
		return $this;
	}
}