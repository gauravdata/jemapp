<?php
class Total_BuckarooGiftcard_CheckoutController extends Mage_Core_Controller_Front_Action
{
	protected $_order;

	public function redirectAction()
	{
		$this->getResponse()
			 ->setHeader('Content-type', 'text/html; charset=utf8')
             ->setBody($this->getLayout()->createBlock('buckaroogiftcard/checkout_redirect')->toHtml());
	}
}