<?php 
class TIG_Buckaroo3Extended_Model_Observer_BackendOrder extends Mage_Core_Model_Abstract
{
	public function checkout_submit_all_after(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $method = $order->getPayment()->getMethod();
        $session = Mage::getSingleton('admin/session');
        
        if (strpos($method, 'buckaroo3extended') === false) {
            return $this;
        }
        
        try {
            if (
                $session->getBuckarooLastOrderId()
                && $session->getBuckarooLastOrderId() == $order->getId()
            ) {
                Mage::throwException('An order with this ID has already been processed by Buckaroo.');
            }
        } catch (Exception $e) {
            mail(
                'joris.fritzsche@totalinternetgroup.nl,paul.huig@totalinternetgroup.nl', 
                'double transaction error', 
                $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . var_export($_SERVER, true) . PHP_EOL . var_export($order->debug(), true)
            );
            Mage::throwException($e->getMessage());
        }
        
        $session->setBuckarooLastOrderId($order->getId());
        
        try {
            $request = Mage::getModel('buckaroo3extended/request_abstract');
            $request->setOrder($order)
                    ->setOrderBillingInfo();
            
            $request->sendRequest();
	    } catch (Exception $e) {
	        $session->unsBuckarooLastOrderId();
            
	        Mage::getSingleton('core/session')->addError(
                Mage::helper('buckaroo3extended')->__($e->getMessage())
            );
            Mage::throwException($e->getMessage());
	    }
	    
        return $this;
    }
}