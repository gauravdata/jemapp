<?php

class Twm_WelcomeTopLink_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getWelcomeMessage()
	{
		$_helper = Mage::helper('customer');
		return $this->__('Welcome, %s', $_helper->getCustomerName());
	}

}