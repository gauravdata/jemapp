<?php

class Twm_ExtendAwPoints_Model_Observer
{
    public function onepageCheckClubJmaValue(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();
        /** @var Mage_Checkout_Model_Type_Onepage $onePage */
        $onePage = Mage::getSingleton('checkout/type_onepage');

        if ($request->isPost())
        {
            if ($request->has('club_jma'))
                $onePage->getQuote()->setData('club_jma', true);
            else
                $onePage->getQuote()->setData('club_jma', false);

            $onePage->getQuote()->save();
        }
    }

    public function onepageSaveClubJmaValue(Varien_Event_Observer $observer)
    {
        /** @var Twm_Sales_Model_Order $order */
        $order = Mage::getSingleton('checkout/session')->getLastRealOrder();
        $quote = Mage::getSingleton('sales/quote')->load($order->getQuoteId());

        Mage::helper('pointsandrewards')->toggleAllFlags((bool)(int)$quote->getData('club_jma'));
    }

    public function accountEditToggleJma(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();

        Mage::helper('pointsandrewards')->toggleAllFlags((bool)(int)$request->getPost('club_jma'));
    }
}