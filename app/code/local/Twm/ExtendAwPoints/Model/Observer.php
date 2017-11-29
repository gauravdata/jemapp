<?php

class Twm_ExtendAwPoints_Model_Observer
{
    public function onepageCheckClubJmaValue(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();

        if ($request->isPost())
        {
            if ($request->has('club_jma'))
                Mage::helper('pointsandrewards')->toggleAllFlags(true);
            else
                Mage::helper('pointsandrewards')->toggleAllFlags(false);
        }

    }
}