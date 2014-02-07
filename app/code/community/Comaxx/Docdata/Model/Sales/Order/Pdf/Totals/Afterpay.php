<?php
/**
 * Model to update the pdf invoice with afterpay fee information
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Order_Pdf_Totals_Afterpay extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	const INCL_EXCL_TAX = '3',
		  INCL_TAX = '2';
		
	/**
	 * Get array of arrays with totals information for display in PDF
	 * array(
	 *  $index => array(
	 *      'amount'   => $amount,
	 *      'label'    => $label,
	 *      'font_size'=> $font_size
	 *  )
	 * )
	 * @return array
	 */
	public function getTotalsForDisplay()
	{
		$pm_code = $pm_name = null;
		$order = $this->getOrder();
		$totals = array();
		
		//check if order is paid via afterpay, if not skip this display entry
		if (count($order->getPaymentsCollection())) {
			$payment = $order->getPayment();
			
			$method = $payment->getMethodInstance();
			$pm_code = $method->getCode();
			$pm_name = $method->getPmName();
			
			if (!($method instanceof Comaxx_Docdata_Model_Method_Fee)) {
				return $totals;
			}
		} else {
			return $totals;
		}
		
		$prefix = $this->getAmountPrefix();
		$display_tax = Mage::helper('docdata/config')->getPaymentMethodItem($pm_code, 'extra_costs_displaytax');
		$fontSize = $this->getFontSize();
		
		//set amounts to be displayed
		$amount = $order->getDocdataFeeAmount();
		$amount_incl_tax = $amount + $order->getDocdataFeeTaxAmount();
		
		//format fields for correct price display
		$amount = $this->getOrder()->formatPriceTxt($amount);
		$amount_incl_tax = $this->getOrder()->formatPriceTxt($amount_incl_tax);
		
		//display items (note fontsize is variable passed by calling class)
		if ($display_tax === self::INCL_EXCL_TAX) {
			$totals = array(
				array(
					'amount'    => $prefix.$amount,
					'label'     => Mage::helper('docdata')->__($pm_name . ' servicekosten (Excl.BTW)') . ':',
					'font_size' => $fontSize
				),
				array(
					'amount'    => $prefix.$amount_incl_tax,
					'label'     => Mage::helper('docdata')->__($pm_name . ' servicekosten (Incl.BTW)') . ':',
					'font_size' => $fontSize
				),
			);
		} elseif ($display_tax === self::INCL_TAX) {
			$totals = array(array(
				'amount'    => $prefix.$amount_incl_tax,
				'label'     => Mage::helper ( 'docdata' )->__($pm_name . ' servicekosten') . ':',
				'font_size' => $fontSize
			));
		} else {
			$totals = array(array(
				'amount'    => $prefix.$amount,
				'label'     => Mage::helper('docdata')->__($pm_name . ' servicekosten (Excl.BTW)') . ':',
				'font_size' => $fontSize
			));
		}
		
		return $totals;
	}
}
