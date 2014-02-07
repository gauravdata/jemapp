<?php
class TIG_Buckaroo3Extended_CheckoutController extends Mage_Core_Controller_Front_Action
{
	public function checkoutAction()
	{
	    $session = Mage::getSingleton('checkout/session');
        $lastOrderId = $session->getLastOrderId();
        
        try{
            if (
                $session->getBuckarooLastOrderId()
                && $session->getBuckarooLastOrderId() == $lastOrderId
            ) {
                Mage::throwException('This transaction has already been sent.');
            }
        } catch (Exception $e) {
            mail(
                'joris.fritzsche@totalinternetgroup.nl,paul.huig@totalinternetgroup.nl', 
                'double transaction error', 
                $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . var_export($_SERVER, true) . PHP_EOL . var_export($session->debug(), true)
            );
            
            $session->addSuccess(
                Mage::helper('buckaroo3extended')->__('Your payment request has been sent.')
            );
            
            $this->_redirect('checkout/onepage/success');
            return $this;
        }
        
        $session->setBuckarooLastOrderId($lastOrderId);
        
        $request = Mage::getModel('buckaroo3extended/request_abstract');
        $request->sendRequest();
	}
    
    public function saveDataAction()
    {
        $data = $this->getRequest()->getPost();
        
        if (!is_array($data) || !isset($data['name']) || !isset($data['value'])) {
            return;
        }
        
        $name = $data['name'];
        $value = $data['value'];
        
        $session = Mage::getSingleton('checkout/session');
        $session->setData($name, $value);
        
        return;
    }
}