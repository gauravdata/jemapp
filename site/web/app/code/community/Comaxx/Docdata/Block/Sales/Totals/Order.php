<?php

class Comaxx_Docdata_Block_Sales_Totals_Order extends Mage_Sales_Block_Order_Totals {
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
		
		if (($method instanceof Comaxx_Docdata_Model_Method_Fee) && $amount) {
			$this->addTotalBefore(new Varien_Object(array(
				'code'      => 'docdata_payment_fee',
				'value'     => $amount,
				'base_value'=> $amount,
				'label'     => $this->helper('docdata')->__($method->getPmName() . ' servicekosten')
				), array('tax'))
			);
		}

		return $this;
	}

}