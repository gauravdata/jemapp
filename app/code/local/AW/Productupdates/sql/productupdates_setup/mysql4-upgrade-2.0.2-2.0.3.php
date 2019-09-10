<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


$installer = $this;
$installer->startSetup();
$setup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

$productTypesArray = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
$productTypes = array_keys($productTypesArray);
$sarpProductTypes = array(
    'subscription_simple',
    'subscription_virtual',
    'subscription_downloadable',
    'subscription_grouped',
    'subscription_configurable',
);
array_merge($productTypes, $sarpProductTypes);
try {
    $setup->updateAttribute('catalog_product', 'notification_title', 'apply_to', implode(',', $productTypes));
    $setup->updateAttribute('catalog_product', 'notification_text', 'apply_to', implode(',', $productTypes));
} catch (Ecxception $e) {
    Mage::logException($e);
}
$installer->endSetup();