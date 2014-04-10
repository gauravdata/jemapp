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

ALTER TABLE {$this->getTable('nostress_export')}
ADD COLUMN  `centrumcategory` varchar(5) NULL default '',     
ADD COLUMN  `start_time_hour` varchar(2) NULL default '00',
ADD COLUMN  `start_time_minute` varchar(2) NULL default '00',
ADD COLUMN  `start_time_second` varchar(2) NULL default '00',
DROP `start_time`,
DROP `product_url_params`;

")->endSetup();

