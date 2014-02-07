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

-- DROP TABLE IF EXISTS {$this->getTable('nostress_export_categoryproducts')};
ALTER TABLE {$this->getTable('nostress_export')}
DROP `category_ids`;
ALTER TABLE {$this->getTable('nostress_export')}
DROP `product_id`;


CREATE TABLE {$this->getTable('nostress_export_categoryproducts')} (
  `entity_id` bigint(20) unsigned NOT NULL auto_increment, 
  `export_id` int(11) unsigned NOT NULL, 
  `category_id` int(10) unsigned NOT NULL, 
  `product_id` int(10) unsigned NOT NULL, 
  FOREIGN KEY (export_id) REFERENCES {$this->getTable('nostress_export')} (export_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (category_id) REFERENCES {$this->getTable('catalog_category_entity')}(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (product_id) REFERENCES {$this->getTable('catalog_product_entity')}(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('nostress_export_records')} (
  `entity_id` bigint(20) unsigned NOT NULL auto_increment, 
  `export_id` int(11) unsigned NOT NULL, 
  `relation_id` bigint(20) unsigned NOT NULL, 
  FOREIGN KEY (export_id) REFERENCES {$this->getTable('nostress_export')}(export_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (relation_id) REFERENCES {$this->getTable('nostress_export_categoryproducts')}(entity_id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();

