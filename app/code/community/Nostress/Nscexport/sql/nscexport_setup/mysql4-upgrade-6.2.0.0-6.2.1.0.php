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

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_cron')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,   
  `export_id` int(11) unsigned NOT NULL, 
  `day_of_week` enum ('1','2','3','4','5','6','7') default '1' NOT NULL,
  `time` TIME default '00:00:00' NOT NULL,
  FOREIGN KEY (export_id) REFERENCES {$this->getTable('nostress_export')} (export_id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup(); 

$collection = Mage::getModel('nscexport/profile')->getCollection()->load();
$days = Mage::getSingleton('nscexport/config_source_dayofweek')->getAllValues();
$cronModel = Mage::getModel('nscexport/cron');
foreach ($collection as $profile)
{
	$time = $profile->getStartTime();
	$hours = substr($time,0,2); 
	$minutes = 	substr($time,strpos($time,":")+1,2);
	if($minutes < 15)
		$minutes = "00";
	else if($minutes >= 15 && $minutes <= 45)
		$minutes = "30";
	else 
	{
		$minutes = "00";
		$hours = $hours+1;
		if($hours == 24)
		 	$hours = "00";
	}
	$hours = str_pad($hours,2,"0",STR_PAD_LEFT);	
	$cronModel->addRecords($profile->getId(),$days,array($hours.":".$minutes));
}


$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('nostress_export')}
DROP `start_time`;

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_plugin')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,   
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `latest_version` varchar(255) NOT NULL,  
  `module_version` varchar(255) NOT NULL,
  `download_link` text NOT NULL,    
  `update_time` datetime NULL,    
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('nostress_export_cache_weee')} ( 
	`product_id` int(10) unsigned NOT NULL,   	
	`website_id` smallint(5) unsigned NOT NULL, 
	`total` decimal(12,4) NOT NULL, 
	`total_final` decimal(12,4) NOT NULL,
	PRIMARY KEY (`product_id`,`website_id`) 
	)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 
