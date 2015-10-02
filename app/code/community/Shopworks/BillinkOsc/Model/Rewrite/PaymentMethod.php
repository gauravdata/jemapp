<?php

class Shopworks_BillinkOsc_Model_Rewrite_PaymentMethod extends Shopworks_Billink_Model_Payment_Method
{
    /**
     * @return bool
     */
    protected function beforeAssignData()
    {
        //If the current action is the OSC set_method_seperate action, than ignore the call
        $continue = !$this->isUrlOscMethodsSeperate();
        return $continue;
    }

    /**
     * @return bool
     */
    protected function beforeValidate()
    {
        //If the current action is the OSC set_method_seperate action, than ignore the call
        $continue = !$this->isUrlOscMethodsSeperate();
        return $continue;
    }


    /**
     * @return bool
     */
    private function isUrlOscMethodsSeperate()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();

        if (strpos($currentUrl,'set_methods_separate') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}