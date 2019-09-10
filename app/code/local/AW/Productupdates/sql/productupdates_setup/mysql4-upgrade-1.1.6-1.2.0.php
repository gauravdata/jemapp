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
$keys = array();
foreach ($productTypesArray as $k=>$v) {
    $keys[] = $k;
}
$productTypes = implode(",", $keys);

$setup->removeAttribute( 'catalog_product', 'notification_title' );
$setup->removeAttribute( 'catalog_product', 'notification_text' );

$setup->addAttribute(
    'catalog_product', 'notification_title',
    array(
        'group'             => 'Product Update Notifications',
        'label'             => 'Notification Title',
        'type'              => 'varchar',
        'input'             => 'text',
        'default'           => '',
        'class'             => 'validate-number',
        //'backend'           => 'catalog/product_attribute_backend_price',
        'frontend'          => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => $productTypes,
    )
);


$setup->addAttribute(
    'catalog_product', 'notification_text',
    array(
        'group'             => 'Product Update Notifications',
        'label'             => 'Notification Text',
        'type'              => 'text',
        'input'             => 'textarea',
        'default'           => '',
        'class'             => 'validate-number',
        //'backend'           => 'catalog/product_attribute_backend_price',
        'frontend'          => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => $productTypes,
    )
);
$installer->endSetup();