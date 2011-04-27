<?php
class Total_Buckaroo_CheckoutController extends Mage_Core_Controller_Front_Action
{
	protected $_order;
	

	public function redirectAction()
	{
		$this->getResponse()
			 ->setHeader('Content-type', 'text/html; charset=utf8')
             ->setBody($this->getLayout()->createBlock('buckaroo/checkout_redirect')->toHtml());
	}
	
	public function successAction()
	{
	    if(empty($_POST))
		{
			echo "Only Buckaroo can call this page properly."; exit;
		}
		
		// Finding out the payment module code
		$order_id=$_POST['bpe_invoice'];
		$order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$payment_code=$order->getPayment()->getMethodInstance()->getCode();
		
		$bpe_result=$_POST['bpe_result'];
		$response=Mage::getSingleton('Total_Buckaroo_Model_PaymentMethod')->process_responsecodes($bpe_result, $order);
		$message=Mage::helper('buckaroo')->__($response['omschrijving']);

		if($response['code']==Total_Buckaroo_Model_PaymentMethod::BUCKAROO_FAILED)
		{
			Mage::getSingleton('core/session')->addError($message);	
			$redirect=trim(Mage::getStoreConfig('payment/buckaroo/failure_redirect', Mage::app()->getStore()->getStoreId()));
		}
		else 
		{
			foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
			    Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
			}
			
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false);
			
			Mage::getSingleton('core/session')->addSuccess($message);
			$redirect=trim(Mage::getStoreConfig('payment/buckaroo/success_redirect', Mage::app()->getStore()->getStoreId()));
		}
		
		return $this->_redirect($redirect);
	}
}