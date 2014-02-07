<?php
/**
 * Block to insert additional fees into invoice totals (Magento backend)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Adminhtml_Sales_Totals_Invoice extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals {
	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals() {
		parent::_initTotals();
		
		$source = $this->getSource();
		$amount = $source->getDocdataFeeAmount();
		$method = $source->getOrder()->getPayment()->getMethodInstance();
		
		
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