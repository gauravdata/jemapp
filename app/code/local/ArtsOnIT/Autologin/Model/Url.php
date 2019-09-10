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
class ArtsOnIT_Autologin_Model_Url extends Mage_Core_Model_Url 
{
	public function setRouteParams(array $data, $unsetOldParams=true)
    {
		if (! Mage::getStoreConfig ( 'customer/autologin/enabled' )) {
 
			return parent::setRouteParams($data, $unsetOldParams);
		}
		
	
		if (isset($data['_autologin']) && (bool)$data['_autologin']) {
			$found = false;
				$hash = '';
			if (isset($data['_autologin_hash'])) {
			 	$hash = $data['_autologin_hash']; 
				$found = true; 
				unset($data['_autologin_hash']); 
			}
			 
			if (!$found && isset($data['_autologin_customer'])) {
           		$customer = $data['_autologin_customer'];
				 
				if (!$customer instanceof Mage_Customer_Model_Customer)
				{
					if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $customer))
					{
					 
						$customer = Mage::getModel('customer/customer')->setWebsiteId($this->getStore()->getWebsiteId())->loadByEmail($customer);
					}
					else
					{
						$customer = Mage::getModel('customer/customer')->load($customer);
					}
					if ($customer->getEntityId() > 0)
					{
						$found = true;
					}
				}
				else
				{
				 
					$found = true;
				}
				 
				if ($found && $customer->getData('autologin_is_active'))
				{
				 
					$hash = $customer->getData('autologin_hash');
				}
				unset($data['_autologin_customer']);
				 
			}
			 
			if(!$found && Mage::helper('customer')->isLoggedIn())
			{
				$hash = Mage::helper('customer')->getCustomer()->getData('autologin_hash');	
				if ($hash != '')
				{
					$found = true;
				}
			}
			 
			if ($found)
			{				 

				$data['_forced_secure']= true; 
				$param = Mage::getStoreConfig ( 'customer/autologin/urlparam', $this->getStore()->getStoreId());
				 
				$this->setQueryParam($param, $hash);
			} 
            unset($data['_autologin']);
	 
        }
		 return parent::setRouteParams($data, $unsetOldParams);
	}
}
