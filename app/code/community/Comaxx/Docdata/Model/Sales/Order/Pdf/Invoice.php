<?php
/**
 * Model to update pdf invoice with additional data such as additional order fees
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice {
	
	/**
	 * Method used by Magento pdf generator to extract totals rows used to update the totals section
	 *
	 * @param Mage_Sales_Model_Order_Invoice $source
	 *
	 * @return array Updated totals list
	 */
	protected function _getTotalsList($source) {
		$totals_list = parent::_getTotalsList($source);
		
		$method = $source->getOrder()->getPayment()->getMethodInstance();
		if (($method instanceof Comaxx_Docdata_Model_Method_Fee)) {
			//add afterpay item to list in case there are afterpay costs
			$new_item = Mage::getModel('docdata/sales_order_pdf_totals_afterpay');
			$new_item->setData(array(
				"title" => Mage::helper('docdata')->__($method->getPmName() . ' servicekosten'),
				"sort_order" => "250",
				"model" => $new_item,
				"font_size" => "7",
				"display_zero" => "1",
			));
			$totals_list[] = $new_item;
			
			//sort in correct order
			usort($totals_list, array($this, '_sortTotalsObjectList'));
		}
		
		return $totals_list;
	}

	
	/**
	 * Method to sort the totals list after editing it
	 *
	 * @param Object $a Object to compare
	 * @param Object $b Object to compare with
	 *
	 * @return int Value 1 if $a has a higher sort order, -1 if $a has a lower sortorder, 0 if sortorders are equal
	 */
	protected function _sortTotalsObjectList($a, $b) {
		$a_sort_order = $a->getSortOrder();
		$b_sort_order = $b->getSortOrder();
		
		if (!isset($a_sort_order) || !isset($b_sort_order)) {
			return 0;
		}

		if ($a_sort_order == $b_sort_order) {
			return 0;
		}

		return ($a_sort_order > $b_sort_order) ? 1 : -1;
	}
}