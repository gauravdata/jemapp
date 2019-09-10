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
class ArtsOnIT_Autologin_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_code_entities_match = array( '&quot;' ,'!' ,'@' ,'#' ,'$' ,'%' ,'^' ,'&' ,'*' ,'(' ,')' ,'+' ,'{' ,'}' ,'|' ,':' ,'"' ,'<' ,'>' ,'?' ,'[' ,']' ,'' ,';' ,"'" ,',' ,'.' ,'_' ,'/' ,'*' ,'+' ,'~' ,'`' ,'=' ,' ' ,'---' ,'--'); 
	public function bulkGenerate()
	{
		$coll = Mage::getModel('customer/entity_customer_collection');	
		$coll->addAttributeToSelect('autologin_hash');

		$coll->walk(array($this,'generateAutologin'));
  
	}
	public function generateAutologin($customer, $force_active = true)
	{
		if (!($customer instanceof Mage_Customer_Model_Customer))
		{
			
			$customer = Mage::getModel('customer/customer')->load($customer);
		}
		if ($force_active)
		{
			if($customer->getAutologinHash() != '')
			{
				return ;
			}
		}
		//do {
			$newHash = $this->generateHash($customer->getEmail() . '-'. $customer->getStoreId());
		//	$unique = (Mage::getModel('customer/entity_customer_collection')->addAttributeToFilter('autologin_hash', $newHash)->count() == 0);
			$customer->setAutologinHash($newHash);
			
		//}
		//while(!$unique);
	    Mage::getResourceSingleton('customer/customer')->saveAttribute($customer, 'autologin_hash');
	    if ($force_active )
	    {
	    	$customer->setData('autologin_is_active',true);
	    	Mage::getResourceSingleton('customer/customer')->saveAttribute($customer, 'autologin_is_active');
	    }
	}
	
	public function generateHash($key)
	{
		$key = $key . md5(uniqid(rand(), true));
		$hash = Mage::helper('core')->encrypt($key);
		$hash = str_replace($this->_code_entities_match, '', $hash); 
		if (strlen($hash) > 254)
		{
			$hash = substr($hash , 0, 254);
		}
		return $hash;
	}
	public function tryLogin($hashs)
	{  
		 $coll = Mage::getModel('customer/entity_customer_collection')
		 	->addAttributeToFilter('autologin_is_active', true)
		 	->addAttributeToFilter('autologin_hash', $hashs)->load();
	  
		 if($coll->count() == 1)
		 {
		 	Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($coll->getFirstItem());
		 	return true;
		 }
		 return false;
	}
}