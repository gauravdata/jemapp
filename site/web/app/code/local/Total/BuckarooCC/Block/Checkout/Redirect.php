<?php
class Total_BuckarooCC_Block_Checkout_Redirect extends Total_Buckaroo_Block_Checkout_Redirect
{
	public $payment_method='creditcard';
	public $_code='buckaroocc';
    
	

	protected function _toHtml()
    {
    		return parent::_toHtml();
    }
}