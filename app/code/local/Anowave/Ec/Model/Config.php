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
class Anowave_Ec_Model_Config
{
	public function notify()
	{
		$api = Mage::getModel('ec/api');
		
		/**
		 * Operation log
		 */
		$log = array();
		
		foreach (@$_POST['args'] as $entry)
		{
			$log = array_merge($log, $api->create($entry));
		}
		
		if (!$log)
		{
			$log[] = Mage::helper('core')->__('Container configured succesfully. Please go to Google Tag Manager to preview newly created tags, variables and triggers.');
		}
		
		if ($log)
		{
			Mage::getSingleton('core/session')->addNotice
			(
				nl2br(join(PHP_EOL, $log))
			);
		}
		
		return Mage::helper('ec')->notify();
	}
}