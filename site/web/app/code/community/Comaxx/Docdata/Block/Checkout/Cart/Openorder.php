<?php
/**
 * Block which indicates to the user that their is stil an open order with option to continue with its payment (or cancel)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Checkout_Cart_Openorder extends Mage_Core_Block_Template {
	
	const NEW_ORDER_STATE = "new";
	
	/**
	 * Constructor
	 *
	 * @return Comaxx_Docdata_Block_Checkout_Cart_Openorder Instance of class
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('comaxx_docdata/checkout/cart/openorder.phtml');
	}
	
	/**
	 * Gets the last open order made by customer (if any)
	 *
	 * @return Mage_Sales_Model_Order|null Last order if found, otherwise null
	 */
	public function getOpenOrder() {
		$order = null;
		$temp_order = null;
		
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$current_customer = Mage::getSingleton('customer/session')->getCustomer();
			
			$orders = Mage::getResourceModel('sales/order_collection')
				->addFieldToSelect('*')
				->addFieldToFilter('customer_id', $current_customer->getId())
				->addAttributeToSort('created_at', 'DESC')
				->setPageSize(1);
				
			if (count($orders) > 0) {
				$temp_order = $orders->getFirstItem();
			}
		} else { //try to get from session
			$increment_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			
			if (!empty($increment_id)) {
				$temp_order = Mage::getModel('sales/order')->load($increment_id, 'increment_id');
			}
		}
		
		if (!empty($temp_order)) {
			//only return order if last order is a docdata order with state still at new
			$order_key = $temp_order->getDocdataPaymentOrderKey();
			if ($temp_order->getState() === self::NEW_ORDER_STATE && !empty($order_key)) {
				$order = $temp_order;
			}
		}
		
		return $order;
	}
	
	/**
	 * Gets Url to cancel order 
	 * 
	 * @param Mage_Sales_Model_Order $order Order to get URL for
	 *
	 * @return string|null Url to cancel the order
	 */
	public function getCancelUrl($order) {
		$result = null;
		
		if (!empty($order) && $order->getDocdataPaymentOrderKey()) {
			$result = Mage::getBaseUrl().'docdata/payment/cancel/id/'.$order->getIncrementId();
		}
		
		return $result;
	}
	
	/**
	 * Gets Url to abort order 
	 * 
	 * @param Mage_Sales_Model_Order $order Order to get URL for
	 *
	 * @return string|null Url to abort the order
	 */
	public function getAbortUrl($order) {
		$result = null;
		
		if (!empty($order) && $order->getDocdataPaymentOrderKey()) {
			$result = Mage::getBaseUrl().'docdata/payment/abort/id/'.$order->getIncrementId();
		}
		
		return $result;
	}
}