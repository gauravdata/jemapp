<?php

class Twm_ExtendAwPoints_Helper_Data
{
    public function canGetPoints()
    {
        try
        {
            $quote = Mage::getModel('checkout/session')->getQuote();
            $quoteData = $quote->getData();
            $grandTotal = $quoteData['grand_total'];

            $minimumOrder = trim(str_replace(',', '.', Mage::getStoreConfig('points/general/minimum_points_amount_for_spend')));

            if ($minimumOrder != '' && $grandTotal >= $minimumOrder)
                return true;
            elseif ($minimumOrder != '' && $grandTotal < $minimumOrder)
                return false;
            else
                return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function hasFlags()
    {
        $customer = Mage::getModel('customer/session')->getCustomer();
        /** @var AW_Points_Model_Summary $summary */
        $summary = Mage::getModel('points/summary')->loadByCustomer($customer);

        return $summary->getBalanceUpdateNotification() && $summary->getPointsExpirationNotification();
    }

    public function isClubJmaMember()
    {
        $customer = Mage::getModel('customer/session')->getCustomer();
        return (bool)(int)$customer->getData('club_jma');
    }

    public function toggleAllFlags($toggle = null)
    {
        try {
            $customer = Mage::getModel('customer/session')->getCustomer();
            $customer->setData('club_jma', $toggle);
            $customer->save();

            $summary = Mage::getModel('points/summary')->loadByCustomer($customer);

            $summary->setBalanceUpdateNotification($toggle)
                    ->setPointsExpirationNotification($toggle)
                    ->setUpdateDate(true)
                    ->save();
        }
        catch (Exception $e)
        {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        }
    }
}