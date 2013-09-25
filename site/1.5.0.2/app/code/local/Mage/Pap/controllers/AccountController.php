<?php

class Mage_Pap_AccountController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(create|login)/i', $action)) {
                $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
/*
        $this->loadLayout();
        $this->_initLayoutMessages('pap/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('pap/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();*/
    }

    public function loginAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

}
