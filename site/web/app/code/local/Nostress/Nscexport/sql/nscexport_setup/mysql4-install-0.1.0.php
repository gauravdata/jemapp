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

-- DROP TABLE IF EXISTS {$this->getTable('nostress_export')};
CREATE TABLE {$this->getTable('nostress_export')} (
  `export_id` int(11) unsigned NOT NULL auto_increment, 
  `name` varchar(255) NOT NULL default '',
  `enabled` int(1) NOT NULL default '0',  
  `frequency` varchar(1) NULL ,
  `filename` varchar(255) character set utf8 default NULL,
  `url` varchar(255) character set utf8 default NULL,
  `searchengine` varchar(255) NOT NULL default '',
  `centrumcategory` varchar(5) NULL default '',     
  `start_time_hour` varchar(2) NULL default '00',
  `start_time_minute` varchar(2) NULL default '00',
  `start_time_second` varchar(2) NULL default '00',
  `category_ids` varchar(1000) NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`export_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
