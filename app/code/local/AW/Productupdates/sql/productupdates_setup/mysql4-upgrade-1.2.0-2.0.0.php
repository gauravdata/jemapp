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


try {
    /* price index tables */
    $priceIndexTable = $this->getTable('catalog/product_index_price');
    $priceIndexAlias = $this->getTable('productupdates/priceindex');
    /* rule index tables */
    $ruleIndexTable = $this->getTable('catalogrule/rule_product_price');
    $ruleIndexAlias = $this->getTable('productupdates/catalogrule');
    /* stock index tables */
    $invIndexTable = $this->getTable('cataloginventory/stock_status');
    $invIndexAlias = $this->getTable('productupdates/inventoryindex');
    /* get create columns for index tables */
    $pattern = "#\((.+),\s*PRIMARY#isu";
    preg_match($pattern, $this->getConnection()->getCreateTable($priceIndexTable), $priceIndex);
    preg_match($pattern, $this->getConnection()->getCreateTable($invIndexTable), $invIndex);
    preg_match($pattern, $this->getConnection()->getCreateTable($ruleIndexTable), $ruleIndex);
 
    $this->startSetup();
    
    $this->run("        
       
         CREATE TABLE IF NOT EXISTS {$ruleIndexAlias} (
              {$ruleIndex[1]},
              PRIMARY KEY (`rule_product_price_id`), 
              KEY `IND_AW_PUN_PRODUCT_ID` (`product_id`),
              KEY `IND_AW_PUN_WEBSITE_ID` (`website_id`),
              KEY `IND_AW_PUN_CUST_GROUP_ID` (`customer_group_id`)    
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;         

         CREATE TABLE IF NOT EXISTS `{$priceIndexAlias}` (
              {$priceIndex[1]},
              PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`),
              KEY `IDX_AW_PUN_CATALOG_INDEX_PRICE_CUSTOMER_GROUP_ID` (`customer_group_id`),
              KEY `IDX_AW_PUN_CATALOG_INDEX_PRICE_WEBSITE_ID` (`website_id`),
              KEY `IDX_AW_PUN_CATALOG_INDEX_PRICE_MIN_PRICE` (`min_price`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          
          CREATE TABLE IF NOT EXISTS `{$invIndexAlias}` (
              {$invIndex[1]},
              PRIMARY KEY (`product_id`,`website_id`,`stock_id`),
              KEY `IDX_AW_PUN_CATALOGINVENTORY_STOCK_STOCK_ID` (`stock_id`),
              KEY `IDX_AW_PUN_CATALOGINVENTORY_STOCK_WEBSITE_ID` (`website_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 
            CREATE TABLE IF NOT EXISTS {$this->getTable('productupdates/schedule')} (
                      `schedule_id` int(10) unsigned NOT NULL auto_increment,
                      `product_id` int(10) unsigned NOT NULL,
                      `store_ids` varchar(255) default NULL,
                      `customer_group_ids` varchar(255) default NULL,
                      `additional` text default NULL,
                      `website_id` int(10) unsigned default NULL,
                      `send_type` tinyint unsigned NOT NULL,                  
                      `status` tinyint unsigned NOT NULL,
                      `locked_by` bigint unsigned default NULL,
                      `source` int(10) unsigned default NULL,
                      `created_at` DATETIME DEFAULT NULL,
                      `processed_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY  (`schedule_id`),         
                    KEY `FK_PUN_SCHEDULE_PRODUCT` (`product_id`),
                    KEY `FK_AW_PUN_SOURCE` (`source`),
                    KEY `KEY_PUN_COMPOSITE_SCHEDULE` (`product_id`,`send_type`,`status`),                
                    CONSTRAINT `FK_PUN_SCHEDULE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog/product')} (`entity_id`) ON DELETE CASCADE
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;         
 

         CREATE TABLE IF NOT EXISTS {$this->getTable('productupdates/queue')} (
                  `queue_id` bigint unsigned NOT NULL auto_increment,
                  `product_id` int(10) unsigned NOT NULL,
                  `customer_id` int(10) unsigned NOT NULL,
                  `schedule_id` int(10) unsigned NOT NULL,
                  `subscriber_id` int(10) unsigned NOT NULL,
                  `store_id` int(10) unsigned default NULL,                  
                  `website_id` int(10) unsigned default NULL,
                  `send_type` tinyint unsigned NOT NULL,
                  `status` tinyint unsigned NOT NULL,
                  `locked_by` bigint unsigned default NULL,
                  `created_at` datetime DEFAULT NULL,
                  `processed_at` datetime DEFAULT NULL,
                PRIMARY KEY  (`queue_id`),         
                KEY `FK_PUN_QUEQUE_PRODUCT` (`product_id`),
                KEY `FK_PUN_QUEUE_SCHEDULE` (`schedule_id`),
                KEY `FK_PUN_QUEUE_SUBSCRIBER` (`subscriber_id`),
                KEY `KEY_PUN_QUEUE_STATUS` (`status`),
                KEY `KEY_PUN_QUEQUE_CUSTOMER` (`customer_id`),
                KEY `KEY_PUN_COMPOSITE_QUEUE` (`product_id`,`status`),
                CONSTRAINT `FK_PUN_QUEQUE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog/product')} (`entity_id`) ON DELETE CASCADE,
                CONSTRAINT `FK_PUN_QUEUE_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES {$this->getTable('productupdates/subscribers')} (`subscriber_id`) ON DELETE CASCADE,
                CONSTRAINT `FK_PUN_QUEUE_SCHEDULE` FOREIGN KEY (`schedule_id`) REFERENCES {$this->getTable('productupdates/schedule')} (`schedule_id`) ON DELETE CASCADE
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;               

          ");              
            
    Mage::getResourceModel('productupdates/catalogrule')
         ->reindexData(array('alias' => $ruleIndexAlias, 'index' => $ruleIndexTable))
         ->reindexData(array('alias' => $priceIndexAlias,'index' => $priceIndexTable))
         ->reindexData(array('alias' => $invIndexAlias,'index' => $invIndexTable))
    ;
       
    /* existing tables optimization */           
    $mainTable = $this->getTable('productupdates/productupdates');   
    $connection = $this->getConnection();    
    $connection->addColumn($mainTable, 'subscription_type', 'TINYINT UNSIGNED DEFAULT NULL, ADD INDEX ( `subscription_type` )');    
    $connection->addColumn($mainTable, 'parent', 'INT(10) UNSIGNED DEFAULT NULL, ADD INDEX ( `parent` )');
    $connection->addColumn($mainTable, 'additional', 'TEXT DEFAULT NULL');
    $connection->dropKey($mainTable, 'product_id');    
    $connection->addKey($mainTable, 'product_id', array('product_id','subscriber_id','subscr_store_id','subscription_type'), 'unique');    
    $connection->addConstraint('FK_AW_PRODUCTSUPDATE_PRODUCT', $mainTable, 'product_id', $this->getTable('catalog/product'), 'entity_id'); 
    /* subscribe all pun subscribers to automatic price change */
    $connection->update($mainTable, array('subscription_type' => AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE), 'subscription_type IS NULL');
    $this->endSetup(); 
    
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, '', true);
}
 