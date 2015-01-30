<?php

class Twm_MinOrderQty_Model_Observer {

	/**
	 * Retrieve shopping cart model object
	 *
	 * @return Mage_Checkout_Model_Cart
	 */
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}


	public function checkMinOrderQty($observer) {
		$event = $observer->getEvent();
		$controller = $event->getControllerAction();
		if ($controller instanceof Mage_Checkout_CartController && $controller->getRequest()->getActionName() == "index") {
			if (!Mage::helper("minorderqty")->validateOrderQty()) {
				$cart = $this->_getCart();
				$minimumQty = Mage::getStoreConfig('sales/minimum_order_qty/qty');

				$warning = Mage::getStoreConfig('sales/minimum_order_qty/message')
					? Mage::getStoreConfig('sales/minimum_order_qty/message')
					: Mage::helper('checkout')->__('Minimum number of products is %s', $minimumQty);

				$cart->getCheckoutSession()->addNotice($warning);
			}
		}
		if ($controller instanceof Idev_OneStepCheckout_IndexController) {
			if (!Mage::helper("minorderqty")->validateOrderQty()) {
				return $event->getControllerAction()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
			}
		}
	}
}