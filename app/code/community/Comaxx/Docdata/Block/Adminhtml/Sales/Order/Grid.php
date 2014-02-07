<?php
/**
 * Block used for the display of the orders grid
 * 
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {
	protected function _prepareColumns() {
		/* add a column to filter with the Docdata payment id */
		$this->addColumn('docdata_payment_id', array(
				'header' => $this->__('Docdata Payment Id'),
				'width' => '100px',
				'index' => 'docdata_payment_id',
			)
		);

		$this->addColumnsOrder('docdata_payment_id', 'real_order_id');
		return parent::_prepareColumns(); 
	}
}
