<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

/* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
$productCollection = Mage::getResourceModel('catalog/product_collection');

/* @var $action Mage_Catalog_Model_Resource_Product_Action */
$action = Mage::getModel('catalog/resource_product_action');
$action->updateAttributes(
    $productCollection->getAllIds(),
    array('send_to_jmango' => '1'),
    Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
);

Mage::getModel('index/indexer')
    ->getProcessByCode(Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE)
    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();