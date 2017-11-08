<?php

class Twm_RequestDob_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();

    }

    public function saveAction()
    {
        $customer = Mage::getSingleton('customer/session');

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $customer->getCustomer();

        $data = $this->getRequest()->getPost();

        $dob = new DateTime();
        $dob->setDate($data['year'], $data['month'], $data['day']);

        $customer->setData('dob', $dob->format('Y-m-d 00:00:00'));
        $customer->save();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
}