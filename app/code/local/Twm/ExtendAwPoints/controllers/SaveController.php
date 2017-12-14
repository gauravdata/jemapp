<?php

class Twm_ExtendAwPoints_SaveController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        Mage::helper('pointsandrewards')->toggleAllFlags(true);

        return $this->_redirectReferer();
    }
}