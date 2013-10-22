<?php
/**
 * Controller used to handle Docdata callbacks
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Sales_Observer {
	
	/*
	 * Sends refund command to DocData if it's a DocData payment
	 *
	 * @param Varien_Event_Observer $observer Event observer
	 *
	 * @return void
	 */
	public function refundPayment($observer) {
		
		$event = $observer->getEvent();
		$order = $event->getCreditmemo()->getOrder();

		// check if the payment was done via Docdata
		$key = $order->getDocdataPaymentOrderKey();
		if ($key != null) {
			// try to send the refund payment 
			$api_model =  Mage::getModel('docdata/magento');
			$helper = Mage::helper('docdata');
			$amount = $event->getCreditmemo()->getBaseGrandTotal();
			
			// try to refund the payment in Docdata backoffice
			$response = $api_model->refundCall(
				$order,
				array('amount' => $amount)
			);
			
			if (!$response || $response->hasError()) {
				$helper->log('Manual refund request of an order failed: '.$order->getRealOrderId(), Zend_Log::WARN);
				Mage::getSingleton('adminhtml/session')->addError('The refund request could not be completed. Please check via the Docdata backoffice');
			} else {
				$helper->log('Refund request of the order '.$order->getRealOrderId(). ' succeeded');
				
				$api_model->setOrderStatus(
					array(
						$api_model::STATUS_PARTIAL_REFUNDED => $helper->__('Requested refund of %s at Docdata backoffice.', $order->getBaseCurrency()->formatTxt($amount))
					)
				);
			}
		}
	}

	/*
	 * Sends cancel command to DocData if it's a DocData payment
	 *
	 * @param Varien_Event_Observer $observer Event observer
	 *
	 * @exception Mage_Core_Exception
	 * @return void
	 */
	public function cancelPayment(Varien_Event_Observer $observer) {
		$event = $observer->getEvent();
		$order = $event->getPayment()->getOrder();

		// check if the payment was done via Docdata and only allow cancel via backend (if it is frontend docdata initiated this cancel)
		$key = $order->getDocdataPaymentOrderKey();
		if ($key != null && Mage::app()->getStore()->isAdmin()) {
			$api_model =  Mage::getModel('docdata/magento');
			$helper = Mage::helper('docdata');
			
			// try to cancel the payment in Docdata backoffice
			$response = $api_model->cancelCall($order);
			
			if ($response->hasError()) {
				$helper->log('Manual cancel of an order failed: '.$order->getRealOrderId(), Zend_Log::WARN);
				throw new Mage_Core_Exception('The order cannot be canceled. Please check via the Docdata backoffice');
			} else {
				$helper->log('Cancel of the order '.$order->getRealOrderId(). ' succeeded');
				$order->registerCancellation();

				$api_model->setOrderStatus(
					array(
						$api_model::STATUS_CLOSED_CANCELED => $helper->__('Canceled through Magento backoffice.')
					)
				);
			} 
		}
	}
}