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

    public function accountEditToggleJma(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();

        Mage::helper('pointsandrewards')->toggleAllFlags((bool)(int)$request->getPost('club_jma'));
    }

    public function salesOrderPaymentCancel(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
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

	// function observes customer save on frontend
	public function customerSaveBefore($observer)
	{
		/** @var Mage_Customer_Model_Customer $customer */
		$customer = $observer->getEvent()->getCustomer();
		if ($customer->isObjectNew() && !Mage::registry('aw_points_current_customer')) {
			Mage::register('aw_points_current_customer', $customer);
		}

		Mage::helper('pointsandrewards')->toggleAllFlags(true);
	}

	public function updatePointsNotificationFromCustomerEdit($observer)
	{
		Mage::helper('pointsandrewards')->toggleAllFlags(true);
	}
}