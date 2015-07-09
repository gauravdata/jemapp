<?php
/**
 * Anowave Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2015 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

class Anowave_Ec_Helper_Data extends Anowave_Package_Helper_Data
{
	/**
	 * Package Stock Keeping Unit
	 * 
	 * @var string
	 */
	protected $package = 'MAGE-GTM';
	
	/**
	 * License config key 
	 * 
	 * @var string
	 */
	protected $config = 'ec/config/license';
	
	/**
	 * Orders
	 * 
	 * @var mixed
	 */
	protected $orders = null;
	
	/**
	 * Check if Facebook Pixel Tracking is enabled
	 * 
	 * @return boolean
	 */
	public function facebook()
	{
		return (bool) Mage::getStoreConfig('ec/facebook/enable');
	}
	
	/**
	 * Get visitor
	 * 
	 * @return number
	 */
	public function getVisitorId()
	{
		if (Mage::getSingleton("customer/session")->isLoggedIn())
		{
			return (int) Mage::getSingleton("customer/session")->getCustomerId();
		}
		
		return 0;
	}
	
	/**
	 * Get visitor login state 
	 * 
	 * @return string
	 */
	public function getVisitorLoginState()
	{
		return Mage::getSingleton("customer/session")->isLoggedIn() ? 'Logged in':'Logged out';
	}
	
	/**
	 * Get visitor type
	 * 
	 * @return string
	 */
	public function getVisitorType()
	{
		return (string) Mage::getModel('customer/group')->load
		(
			Mage::getSingleton("customer/session")->getCustomerGroupId()
		)->getCode();
	}
	
	public function getVisitorLifetimeValue()
	{
		$value = 0;
		
		foreach ($this->getOrders() as $order) 
		{
			$value += $order->getGrandTotal();
		}
		
		if (Mage::getSingleton("customer/session")->isLoggedIn()) 
		{
			return round($value,2);
		} 
		
		return 0;
	}
	
	/**
	 * Get visitor existing customer
	 * @return string
	 */
	public function getVisitorExistingCustomer()
	{
		return $this->getVisitorLifetimeValue() ? 'Yes' : 'No';
	}
	
	/**
	 * Load customer orders
	 */
	protected function getOrders()
	{
		if (!$this->orders)
		{
			$this->orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter('customer_id',Mage::getSingleton("customer/session")->getId());
		}

		return $this->orders;
	}
}