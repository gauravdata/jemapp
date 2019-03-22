<?php
class WHAP_AWpointsNotification_Model_Notification extends AW_Points_Model_Observer
{
	// function observes customer save on frontend
	public function customerSaveBefore($observer)
	{
		/** @var Mage_Customer_Model_Customer $customer */
		$customer = $observer->getEvent()->getCustomer();
		if ($customer->isObjectNew() && !Mage::registry('aw_points_current_customer')) {
			Mage::register('aw_points_current_customer', $customer);

			$summary = Mage::getModel('points/summary')
			               ->loadByCustomer(
				               $observer->getCustomer()
			               );
			$summary
				->setBalanceUpdateNotification(1)
				->setPointsExpirationNotification(1)
				->setUpdateDate(true)
				->save();
		}
	}
}