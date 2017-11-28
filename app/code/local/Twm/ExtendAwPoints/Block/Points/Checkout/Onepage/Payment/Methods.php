<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 21-11-17
 * Time: 17:08
 */ 
class Twm_ExtendAwPoints_Block_Points_Checkout_Onepage_Payment_Methods extends AW_Points_Block_Checkout_Onepage_Payment_Methods
{
    public function getFreePaymentMethod()
    {
        return Mage::getModel('pointsandrewards/method_pointsandrewards');
    }

    public function pointsSectionAvailable()
    {
        if (Mage::helper('pointsandrewards')->canGetPoints())
            return parent::pointsSectionAvailable();
        else
            return false;
    }
}