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

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$productTypesArray = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
$keys = array();
foreach ($productTypesArray as $k=>$v) {
    $keys[] = $k;
}
$productTypes = implode(",", $keys);
$setup->addAttribute(
    'catalog_product', 'notification_title',
    array(
        'group'             => 'Product Update Notifications',
        'label'             => 'Notification Title',
        'type'              => 'varchar',
        'input'             => 'text',
        'default'           => '',
        'class'             => 'validate-number',
        'backend'           => 'catalog/product_attribute_backend_price',
        'frontend'          => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
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
        'backend'           => 'catalog/product_attribute_backend_price',
        'frontend'          => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
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

/*
 *  
  drop table if exists {$this->getTable('productupdates/catalogrule')};
  drop table if exists {$this->getTable('productupdates/inventoryindex')};    
  drop table if exists {$this->getTable('productupdates/priceindex')};
  drop table if exists {$this->getTable('productupdates/queue')};
  drop table if exists {$this->getTable('productupdates/schedule')};
  drop table if exists {$this->getTable('productupdates/productupdates')};
  drop table if exists {$this->getTable('productupdates/subscribers')};
  
 */

$installer->run("  
 
 
CREATE TABLE IF NOT EXISTS {$this->getTable('productupdates/subscribers')} (
  `subscriber_id` int(10) unsigned NOT NULL auto_increment,
  `fullname` varchar(245) NOT NULL,
  `email` varchar(245) NOT NULL,
  `subscription_date` datetime NOT NULL,
  `reg_id` int(10) unsigned NOT NULL,
PRIMARY KEY  (`subscriber_id`),
UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 
CREATE TABLE IF NOT EXISTS {$this->getTable('productupdates/productupdates')} (
  `productupdates_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `subscriber_id` int(10) unsigned NOT NULL,
  `for_send` tinyint(1) NOT NULL,
PRIMARY KEY  (`productupdates_id`),
UNIQUE KEY `product_id` (`product_id`,`subscriber_id`),
KEY `FK_subscriber` (`subscriber_id`),
CONSTRAINT `FK_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES {$this->getTable('productupdates/subscribers')} (`subscriber_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
