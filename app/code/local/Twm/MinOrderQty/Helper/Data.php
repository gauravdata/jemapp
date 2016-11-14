<?php

class Twm_MinOrderQty_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieve shopping cart model object
	 *
	 * @return Mage_Checkout_Model_Cart
	 */
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	public function validateOrderQty() {
		if (Mage::getStoreConfig('sales/minimum_order_qty/active')) {
			$cart = $this->_getCart();
			if ($cart->getQuote()->getItemsCount()) {
				$minimumQty = Mage::getStoreConfig('sales/minimum_order_qty/qty');

				//check if coupon code for exception was used
				$couponCodeException = Mage::getStoreConfig('sales/minimum_order_qty/coupon_exception');
				$ruleIds = $cart->getQuote()->getAppliedRuleIds();
				if ($couponCodeException && $ruleIds) {
					$couponCodeException = explode(",",$couponCodeException);
					$ruleIds = explode(",",$ruleIds);
					foreach ($ruleIds as $ruleId) {
						if (in_array($ruleId,$couponCodeException)) {
							return true;
						}
					}
				}

				if ($cart->getQuote()->getItemsQty() < $minimumQty) {
					return false;
				}
			}
		}
		return true;
	}
}
