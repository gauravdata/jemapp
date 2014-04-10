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
* Sql update skript
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

$this->startSetup()->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_enginecategory')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,   
  `engine_code` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL DEFAULT 'en_UK',
  `name` varchar(255) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT '-1',
  `path` text CHARACTER SET utf8 DEFAULT '' NOT NULL,
  `ids_path` text CHARACTER SET utf8 DEFAULT '' NOT NULL, 
  `level` int(11) NOT NULL DEFAULT '-1', 
  `parent_name` varchar(255) DEFAULT '',
  `parent_id` int(11) DEFAULT '-1',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('nostress_export')}
ADD COLUMN `start_time` time DEFAULT '00:00:00' AFTER `frequency`,
ADD COLUMN `product_url_params` varchar(255) DEFAULT NULL AFTER `url`,
DROP `centrumcategory`,
DROP `start_time_hour`,
DROP `start_time_minute`,
DROP `start_time_second`;
    
")->endSetup();

