<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   2.4.5
 * @build     1677
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_rma');
if ($version == '1.0.22') {
    return;
} elseif ($version != '1.0.21') {
    die('Please, run migration Rma 1.0.22');
}
$installer->startSetup();

//copy offline items
$items = Mage::getModel('rma/item')->getCollection()
        ->addFieldToFilter('is_removed', 0);
foreach($items as $item) {
    if (!$item->getOfflineOrderName()) {
        continue;
    }
    $customerId = $item->getRma()->getCustomerId();
    $offlineOrder = Mage::helper('rma/offlineOrder')->getOfflineOrder($customerId, $item->getOfflineOrderName());

    Mage::getModel('rma/offline_item')
        ->setData($item->getData())
        ->setOfflineOrderId($offlineOrder->getId())
        ->save();
    $item->setIsRemoved(true)
         ->save();

}

//copy order ids from rmas
$items = Mage::getModel('rma/item')->getCollection();
foreach($items as $item) {
    if ($item->getOrderId()) {
        continue;
    }
    $item->setOrderId($item->getRma()->getOrderId())
          ->save();
}
$installer->endSetup();
