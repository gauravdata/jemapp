<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Sql instalation skript
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_feed')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,   
  `code` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `enabled` int(1) NOT NULL default '0', 
  `type` varchar(255) NULL,  
  `country` varchar(255) NOT NULL DEFAULT 'INTERNATIONAL',
  `file_type` varchar(255) NOT NULL DEFAULT 'xml',
  `taxonomy_code` varchar(255) NULL,
  `layout` text NOT NULL, 
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_taxonomy_setup')} (
	`entity_id` int(11) unsigned NOT NULL auto_increment, 
	`name` varchar(255) NOT NULL,
	`code` varchar(255) NOT NULL,
	`type` varchar(255) NULL,  
	`setup` text NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('nostress_export_enginecategory')} 
	CHANGE `engine_code` `taxonomy_code` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

ALTER TABLE {$this->getTable('nostress_export')} 
	CHANGE `searchengine` `feed` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''; 
");

$installer->endSetup(); 
