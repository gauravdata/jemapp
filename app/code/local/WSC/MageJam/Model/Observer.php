<?php

class WSC_MageJam_Model_Observer
{
    /**
     * Intercepts event that is fired from action checkout/onepage/success.
     * If this event is fired, order successfully placed and payed
     *
     * @param $event
     */
    public function checkoutOnepageControllerSuccessAction($event)
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        if(!$session->getData('magejam')) {
            return;
        }
        $session->setData('success', true);
        if(is_array($event->getOrderIds())) {
            $session->setData('order_id', end($event->getOrderIds()));
        }

        $this->_redirect();
    }


    /**
     * Intercepts cart predispatch event. If hosted methods returns errors, they will be caught here
     */
    public function controllerActionPredispatchCheckoutCartIndex()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        if(!$session->getData('magejam')) {
            return;
        }
        $session->unsetData('magejam');
        $session->setData('success', false);

        $this->_redirect();
    }

    /**
     * Set special variable in user canceled paypal standard method
     */
    public function controllerActionPredispatchPaypalStandardCancel()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        if(!$session->getData('magejam')) {
            return;
        }
        $session->setData('canceled', true);
    }

    /**
     * Internal method used for redirects to 'magejam/payment/result'
     */
    protected function _redirect()
    {
        $redirectUrl = Mage::getUrl('magejam/payment/result', array( '_secure'=>true ));
        header("Location: {$redirectUrl}");
        exit;
    }
}