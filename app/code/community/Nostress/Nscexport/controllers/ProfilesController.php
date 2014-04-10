<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */

/**
 * Frontend kontroler pro exportni modul
 *
 * @category Nostress
 * @package Nostress_Nscexport
 *
 */

class Nostress_Nscexport_ProfilesController extends Mage_Core_Controller_Front_Action 
{
	const PROFILES = 'profiles';
	
	public function runAction() 
	{
		try 
		{
		   	$helper = Mage::helper('nscexport/data_profile'); 	
		   	$profiles = null;
		   	$profiles = $this->getRequest()->getParam(self::PROFILES);
		   	if(isset($profiles))
		   	{		   			
				$profiles = explode(",",$profiles);
				$profileNames = array();
				$profileIds = array();
				foreach ($profiles as $profile)
				{
					if(is_numeric($profile))
						$profileIds[] = $profile;
					else
						$profileNames[] = $profile;
				}
				// run profile using profile IDs or names
				$helper->runProfilesByNames($profileNames);
				$helper->runProfilesByIds($profileIds);
		   	}
		   	else 
		   		$helper->runAllProfiles();
		} 
		catch (Exception $e) 
		{
		    Mage::printException($e);
		}
	}

}