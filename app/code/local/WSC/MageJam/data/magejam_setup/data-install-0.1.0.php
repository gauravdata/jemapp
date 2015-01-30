<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

Mage::getModel('index/indexer')
    ->getProcessByCode(Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE)
    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();