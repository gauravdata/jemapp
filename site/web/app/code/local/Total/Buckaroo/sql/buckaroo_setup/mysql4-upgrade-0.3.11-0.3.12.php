<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Total
 * @package     Total_Buckaroo
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$version15 = '1.5.0.0';
$isVersion15 = version_compare(Mage::getVersion(), $version15, '<') ? false : true;

if (!$isVersion15) {
	return;
}

$statuses = array(
    0 => array('status' =>'buckaroo_success',  'label' => 'Buckaroo (success)',  'is_new' => 1, 'form_key' => '', 'store_labels' => array(), 'state' => 'processing'), 
    1 => array('status' =>'buckaroo_failed',   'label' => 'Buckaroo (failed)',   'is_new' => 1, 'form_key' => '', 'store_labels' => array(), 'state' => 'processing'),
    2 => array('status' =>'buckaroo_too_less', 'label' => 'Buckaroo (too less)', 'is_new' => 1, 'form_key' => '', 'store_labels' => array(), 'state' => 'processing'),
    3 => array('status' =>'buckaroo_too_much', 'label' => 'Buckaroo (too much)', 'is_new' => 1, 'form_key' => '', 'store_labels' => array(), 'state' => 'processing'));

foreach ($statuses as $status) {
	$_stat = Mage::getModel('sales/order_status')->load($status['status']);
	
    /* Add Status */
    if ($status['is_new'] && $_stat->getStatus()) {
        return;
    }
    $_stat->setData($status)->setStatus($status['status']);
    try {
        $_stat->save();
    } catch (Mage_Core_Exception $e) {  } 
    catch (Exception $e) {  }
    
	/* Assign Status to State */
    if ($_stat && $_stat->getStatus()) {
        try {
            $_stat->assignState($status['state'], false);
        } catch (Mage_Core_Exception $e) {  } 
        catch (Exception $e) {  }
    }
}

$installer->endSetup();