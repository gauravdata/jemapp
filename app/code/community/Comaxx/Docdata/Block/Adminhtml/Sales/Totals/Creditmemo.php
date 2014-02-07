<?php
/**
 * Block to insert additional fees into order totals (Magento backend)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Adminhtml_Sales_Totals_Creditmemo extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals {
	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals() {
		parent::_initTotals();
		$order = $this->getSource()->getOrder();
		$amount = $order->getDocdataFeeAmount();
		$tax = $order->getDocdataFeeTaxAmount();
		
		$method = $order->getPayment()->getMethodInstance();
		
		if (($method instanceof Comaxx_Docdata_Model_Method_Fee) && $amount) {
			$this->addTotalBefore(new Varien_Object(array(
				'code'      => 'docdata_payment_fee',
				'value'     => $amount,
				'base_value'=> $amount,
				'label'     => $this->helper('docdata')->__($method->getPmName() . ' servicekosten')
				), array('tax'))
			);
		
			//update totals for creditmemo since Magento does not use order/invoice grand totals
			$creditmemo = $this->getCreditMemo();
			//set tax
			$creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $tax);
			$creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $tax);
			//set grand total
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount + $tax);
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount + $tax);
		}
		return $this;
	}

}