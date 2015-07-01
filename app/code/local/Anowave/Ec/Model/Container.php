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
class Anowave_Ec_Model_Container extends Mage_Core_Model_Config_Data
{
	public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
	{
		$containers = array();
		
		foreach($this->getContainers() as $container)
		{
			$containers[] = "Container: <strong>$container->publicId</strong>,  Container ID: <strong>$container->containerId</strong>";
		}

		
		return nl2br(join(PHP_EOL, $containers));
	}
	
	private function getContainers()
	{
		$account = Mage::getStoreConfig('ec/api/google_gtm_account_id');
		
		if ($account)
		{
			return Mage::getModel('ec/api')->getContainers($account);
		}
		 
		return array();
	}
}