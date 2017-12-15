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

    public function salesOrderPaymentCancel(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getPayment()->getOrder();
        $cancelledTransaction = Mage::getModel('points/transaction')->loadByOrder($order);
        $balanceChange = abs($cancelledTransaction->getData('balance_change'));

        $expirationDays = Mage::getStoreConfig(AW_Points_Helper_Config::POINTS_EXPIRATION_DAYS);
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        Mage::getModel('points/api')->addTransaction(
            $balanceChange,
            'added_by_admin',
            $customer,
            null,
            array(
                'comment' => Mage::helper('translationhelper')->__('Refund for cancelled order #%s', $order->getData('increment_id'))
            ),
            array(
                'points_expiration_days' => $expirationDays
            )
        );

        $a = false;
    }
}