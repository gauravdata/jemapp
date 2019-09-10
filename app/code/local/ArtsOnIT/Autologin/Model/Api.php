<?php
/**
 * ArtsOnIT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://www.mageext.com/respository/docs/License-SourceCode.pdf
 *
 * @category   ArtsOnIT
 * @package    ArtsOnIT_Autologin
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 ArtsonIT di Calore (http://www.mageext.com)
 * @license    http://www.mageext.com/respository/docs/License-SourceCode.pdf
 */
class ArtsOnIT_Autologin_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	public function getHash($customerId)
	{
		$customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId() || !$customer->getAutologinIsActive())
        {
            $this->_fault('not_exists');
            // If customer not found.
        }
        return $customer->getData('autologin_hash');
	}
	
	public function getUrl($customerId, $store_id=0, $store_url='')
	{
		$customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId() || !$customer->getAutologinIsActive())
        {
            $this->_fault('not_exists');
            // If customer not found.
        }
        $store_id = ($store_id == 0) ? $customer->getStoreId() : store_id;

        return Mage::getUrl($store_url, array(
			'_autologin'=> true,
			'_autologin_customer'=> $customer,
			'_store' => $store_id,
			'_forced_secure' => true
		));
	}
	
	public function items()
	{
		$collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('autologin_hash')
            ->addAttributeToSelect('autologin_is_active')
            ->addAttributeToSelect('email');
        
		$result = array();
        foreach ($collection as $customer) {
        	$r = array();
        	$r['customer_id'] = $customer->getId();
        	$r['email'] = $customer->getEmail();
        	$r['firstname'] = $customer->getFirstname();
        	$r['lastname'] = $customer->getLastname();
        	$r['website_id'] = $customer->getWebsiteId();
        	$r['store_id'] = $customer->getStoreId();
            $r['group_id'] = $customer->getGroupId();
            $r['hash'] = $customer->getAutologinHash();
            $r['is_enabled'] = $customer->getAutologinIsActive();
            $result[] = $r;
        } 
        return $result;
	
	}
	
	public function renewHash($customerId)
	{
		$customer = Mage::getModel('customer/customer')->load($customerId);
		if (!$customer->getId())
        {
            $this->_fault('not_exists');
            // If customer not found.
        }
        Mage::helper('autologin')->generateAutologin($customer);
        return true;
	}
}