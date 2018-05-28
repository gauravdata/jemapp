<?php

class Twm_ExtendAwPoints_SaveController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        Mage::helper('pointsandrewards')->toggleAllFlags((bool)(int)$this->getRequest()->getPost('club_jma'));

        return $this->_redirectReferer();
    }
}