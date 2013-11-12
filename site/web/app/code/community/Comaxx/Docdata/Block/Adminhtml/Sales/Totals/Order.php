<?php
/**
 * Block to insert additional fees into order totals (Magento backend)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Adminhtml_Sales_Totals_Order extends Mage_Adminhtml_Block_Sales_Order_Totals {
	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals() {
		parent::_initTotals();
		
		$order = $this->getSource();
		$amount = $order->getDocdataFeeAmount();
		$method = $order->getPayment()->getMethodInstance();
		
		if ($amount) {
			$label = ($method instanceof Comaxx_Docdata_Model_Method_Fee)
				? $method->getPmName() . ' servicekosten'
				: 'servicekosten';
			
			$this->addTotalBefore(new Varien_Object(array(
				'code'      => 'docdata_payment_fee',
				'value'     => $amount,
				'base_value'=> $amount,
				'label'     => $this->helper('docdata')->__($label)
				), array('tax'))
			);
		}

		return $this;
	}

}