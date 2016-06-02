<?php
$installer = $this;
$installer->startSetup();

$installer->run("
  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/setting')}` (
    `setting_id` int(11) NOT NULL AUTO_INCREMENT,
    `visible` tinyint(1) DEFAULT '1',
    `name` varchar(100) NOT NULL,
    `value` text,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `label` varchar(32) DEFAULT NULL,
    `category_id` int(8) DEFAULT '1',
    `field_type` int(4) DEFAULT '1',
    `is_editable_key` int(4) DEFAULT '1',
    `is_deletable` int(4) DEFAULT '1',
    PRIMARY KEY (`setting_id`),
    UNIQUE INDEX (`name`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/category')}` (
    `category_id` int(8) NOT NULL AUTO_INCREMENT,
    `category_name` varchar(32) NOT NULL,
    `is_active` int(1) NOT NULL,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sort_order` int(11) NOT NULL,
    PRIMARY KEY (`category_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/option')}` (
      `option_id` int(8) NOT NULL AUTO_INCREMENT,
      `setting_key` text NOT NULL,
      `label` varchar(32) NOT NULL,
      `value` varchar(32) NOT NULL,
      PRIMARY KEY (`option_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_creditorder')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `creditorder_id` int(11) NOT NULL,
    `last_sync` datetime NOT NULL,
    `status_message` text NOT NULL,
    `raw_xml_response` text NOT NULL,
    `state` int(1) NOT NULL,
    `exact_id` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`creditorder_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_customer')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `last_sync` timestamp NOT NULL,
    `status_message` text NOT NULL,
    `raw_xml_response` text NOT NULL,
    `state` int(1) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`customer_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_order')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `last_sync` datetime NOT NULL,
    `status_message` text NOT NULL,
    `raw_xml_response` text NOT NULL,
    `state` int(1) NOT NULL,
    `exact_id` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`order_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_product')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `last_sync` timestamp NULL DEFAULT NULL,
    `status_message` text NOT NULL,
    `raw_xml_response` text,
    `state` int(1) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`product_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/address')}` (
    `entity_id` int(11) NOT NULL AUTO_INCREMENT,
    `guid` varchar(48) NOT NULL,
    `customer_id` int NOT NULL,
    `address` varchar(128) NOT NULL,
    PRIMARY KEY (`entity_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_shipment')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `shipment_id` int(11) NOT NULL,
    `last_sync` datetime NOT NULL,
    `status_message` text NOT NULL,
    `raw_xml_response` text NOT NULL,
    `state` int(1) NOT NULL,
    `exact_id` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`shipment_id`)
  );

  CREATE TABLE IF NOT EXISTS `{$installer->getTable('exactonline/log_guest')}` (
    `guest_id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `debtor_id` INT(11) NULL,
    PRIMARY KEY (`guest_id`)
  );

  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (1,'General','1',CURRENT_TIMESTAMP,1);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (2,'Payment conditions','1',CURRENT_TIMESTAMP,3);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (3,'VAT-Codes','1',CURRENT_TIMESTAMP,2);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (4,'Sync. Data','1',CURRENT_TIMESTAMP,4);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (5,'Financial','1',CURRENT_TIMESTAMP,5);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (6,'Debtors','1',CURRENT_TIMESTAMP,6);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (7,'Testing','1',CURRENT_TIMESTAMP,9);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (8,'Product Import','1',CURRENT_TIMESTAMP,7);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (9,'Customer Import','1',CURRENT_TIMESTAMP,8);
  INSERT INTO `{$installer->getTable('exactonline/category')}` (`category_id`, `category_name`,`is_active`,`timestamp`,`sort_order`) VALUES (10,'Cost centres','1',CURRENT_TIMESTAMP,10);

  TRUNCATE `{$installer->getTable('exactonline/setting')}`;

  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'link_enabled', 'false', '2001-01-01 00:00:00', 'Koppeling ingeschakeld?', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'partnerkey', '{20850f27-d05e-4d91-9583-96e6fcceb5ea}', '2001-01-01 00:00:00', 'Partnerkey', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'exact_division', 'Division ID', '2001-01-01 00:00:00', 'Exact division', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'shipment_sku', 'VZK', '2001-01-01 00:00:00', 'Artikel voor verzendkosten', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'klantid_range', 'Customer ID range (numeric)', '2001-01-01 00:00:00', 'Customer ID range', 6, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'guest_debtor_range', 'Guest ID Range', '2001-01-01 00:00:00', 'Guest ID Range', 6, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'code_verzameldebiteur_gasten', 'Customer ID Guest Orders', '2001-01-01 00:00:00', 'Debtor ID Guests', 6, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'use_verzameldebiteur', '0', '2001-01-01 00:00:00', 'Alle orders op verzameldebiteur', 6, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'update_guest_debtor', '0', '2001-01-01 00:00:00', 'Bijwerken gastklant', 6, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'create_guest_debtor', '0', '2001-01-01 00:00:00', 'Aanmaken gastklant', 6, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'currency', 'EUR', '2001-01-01 00:00:00', 'Currency Exact Online', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'transaction_order_journal', 'Journal code Exact Online (numeric)', '2001-01-01 00:00:00', 'Journal', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'transaction_credit_journal', 'Credit Journal code', '2001-01-01 00:00:00', 'Credit Journal', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'transaction_glaccount_debtor', 'GL Account Debtors (1300)', '2001-01-01 00:00:00', 'GL Account debtors', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_default', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account (Default)', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_nl', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account NL', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_eu', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account EU', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_noneu', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account Non EU', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_shipment', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account shipping costs', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'gl_account_discount', 'N.v.t.', '2001-01-01 00:00:00', 'GL Account discounts', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'use_costcenter_costunit', '0', '2001-01-01 00:00:00', 'Gebruik kostenplaat/kostendrager', 5, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'payment_condition', 'Default Payment condition (VB)', '2001-01-01 00:00:00', 'Payment condition (Default)', 2, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'products_btw', 'including', '2001-01-01 00:00:00', 'Products shop Inc./Excl. VAT', 5, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'productsyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last Product Sync.', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'customersyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last customer sync.', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'ordersyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last order sync.', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'creditordersyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last credit order sync', 4, 1, 1, 1);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sleep_milliseconds', '1000', '2001-01-01 00:00:00', 'Timeout between requests (ms)', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'lock', '0', '2001-01-01 00:00:00', 'Locked', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(0, 'running_since', '2001-01-1 00:00:00', '2001-01-01 00:00:00', 'Running since', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(0, 'max_script_runtime', '20', '2001-01-01 00:00:00', 'Max script runtime', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'last_synced_exact_order', '0', '2001-01-01 00:00:00', 'Last Exact Order', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'order_sync_status', 'complete,closed', '2001-01-01 00:00:00', 'Order states', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, '0.0000', 'VAT-Code Exact Online', '2001-01-01 00:00:00', 'VAT-code 0.0000%', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, '6.0000', 'VAT-Code Exact Online', '2001-01-01 00:00:00', 'VAT-code 6.0000%', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, '21.0000', 'VAT-Code Exact Online', '2001-01-01 00:00:00', 'VAT-code 21.0000%', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'vatcode_eu', 'VAT-Code Exact Online', '2001-01-01 00:00:00', 'VAT-code EU', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'vatcode_non_eu', 'VAT-Code Exact Online', '2001-01-01 00:00:00', 'VAT-code Non-EU', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'shipping_tax_percent', 'VAT-Code Shipping', '2001-01-01 00:00:00', 'VAT % Shipping costs', 3, 1, 1, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'btw_use_country_id', 'false', '2001-01-01 00:00:00', 'Use country code', 3, 2, 1, 1);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'btw_country_id', 'NL', '2001-01-01 00:00:00', 'Country code', 3, 1, 1, 1);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'tax_address', 'levering', '2001-01-01 00:00:00', 'BTW op basis van welk adres', 3, 1, 1, 1);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'resync_failed_orders', '0', '2001-01-01 00:00:00', 'Resync failed orders', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'download_id', 'D4D', '2001-01-01 00:00:00', 'Download ID Exact', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'collection_limit', '250', '2001-01-01 00:00:00', 'Collection Limit', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_product_changes', '1', '1', 'Only sync product changes', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_customer_changes', '1', '1', 'Only sync customer changes', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_product_images', '0', '1', 'Sync product images', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_itemgroup', 'STANDAARD', '2001-01-01 00:00:00', 'Default Item group', 5, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'enable_products', '0', '2001-01-01 00:00:00', 'synchroniseer producten', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'enable_customers', '0', '2001-01-01 00:00:00', 'synchroniseer klanten', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'enable_orders', '0', '2001-01-01 00:00:00', 'synchroniseer bestellingen', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'enable_delivery', '0', '2001-01-01 00:00:00', 'synchroniseer leveringen', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'enable_stock', '0', '2001-01-01 00:00:00', 'synchroniseer voorraad', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_mode', '0', '2001-01-01 00:00:00', 'Debug Mode', 7, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_order_id', '', '2001-01-01 00:00:00', 'Order ID', 7, 5, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_credit_id', '', '2001-01-01 00:00:00', 'Creditmemo ID', 7, 5, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_order_xml_sent', '', '2001-01-01 00:00:00', 'XML Sent', 7, 4, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_order_xml_received', '', '2001-01-01 00:00:00', 'XML Received', 7, 4, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_credit_xml_sent', '', '2001-01-01 00:00:00', 'XML Sent', 7, 4, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'debug_credit_xml_received', '', '2001-01-01 00:00:00', 'XML Received', 7, 4, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'deliverysyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last Delivery Sync.', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'resync_failed_deliveries', '0', '2001-01-01 00:00:00', 'Resync. Failed Deliveries ', 4, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'description_type', 'standard', '2001-01-01 00:00:00', 'Description Exact Online', 1, 2, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'special_order_sync_status', '', '2001-01-01 00:00:00', 'Special Order States', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'special_order_sync_paymentmethod', '', '2001-01-01 00:00:00', 'Special Payment Methods', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'tax_class_default', '4', '2013-11-27 10:00:00', 'VAT-Code Default', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'tax_class_4', '4', '2013-11-27 10:00:00', 'VAT-High', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_name', 'Description', '2013-11-27 10:00:00', 'Product name', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_description', 'ExtraDescription', '2013-11-27 10:00:00', 'Description', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_short_description', 'ExtraDescription', '2013-11-27 10:00:00', 'Short Description', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_weight', '1', '2013-11 27 10:00:00', 'Default Weight', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_webshop', '1', '2013-11-27 10:00:00', 'Webshop Item', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_website_ids', '1', '2013-11-27 10:00:00', 'Website Ids', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_sync_images', '1', '2013-11-27 10:00:00', 'Sync. Images', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'assortment_parent', 'Assortment Class', '2013-11-27 10:00:00', 'Parent Assortment', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'assortment_children', 'Assortment Class', '2013-11-27 10:00:00', 'Sub Assortment', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'category_calculation', 'assortment', '2013-11-27 10:00:00', 'Determine Category', '8', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_attribute_set', '4', '2013-11-27 10:00:00', 'Attribute Set', '8', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_direction', 'me', '2015-02-27 10:00:00', 'Customer Sync.', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'is_sales', '1', '2015-02-27 10:00:00', 'Only Sync. Debtors', '9', '2', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_country', 'NL', '2015-02-27 10:00:00', 'Default Country', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_phone', '-', '2015-02-27 10:00:00', 'Default Phone Number', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_store_id', '1', '2015-02-27 10:00:00', 'Default Store ID', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_classification', '1', '2015-02-27 10:00:00', 'Default Classification', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_group_id', '1', '2015-02-27 10:00:00', 'Default Group ID', '9', '1', '0', '0');
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_language', 'NL', '2015-02-27 10:00:00', 'Default Language', '6', '1', '0', '0');

  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sku_payment_fee', 'AFP', '2001-01-01 00:00:00', 'Artikel voor PSP-kosten', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'vatcode_payment_fee', 'null', '2001-01-01 00:00:00', 'BTW-code voor PSP-kosten', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'calculate_tax_payment_fee', '0', '2001-01-01 00:00:00', 'Voeg BTW toe aan PSP-kosten', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_warehouse', '1', '2001-01-01 00:00:00', 'Standaard magazijn', 1, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'warehouse_codes', '1', '2001-01-01 00:00:00', 'Magazijnen', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'product_warehouse_codes', '1', '2001-01-01 00:00:00', 'Gekoppelde magazijnen product', 4, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'classification_id', '1', '2001-01-01 00:00:00', 'Classificatie ID', 9, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'classification_value', '1', '2001-01-01 00:00:00', 'Classificatie Waarde', 9, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'store_classification_id', '1', '2001-01-01 00:00:00', 'Standaard Store Classificatie', 9, 1, 0, 0);
  INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'default_website_id', '1', '2001-01-01 00:00:00', 'Standaard Website ID', 8, 1, 0, 0);

  INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES
      ('link_enabled', 'Ja', 'true'),
      ('link_enabled', 'Nee', 'false'),
      ('lock', 'Yes', '1'),
      ('lock', 'No', '0'),
      ('products_btw', 'Including', 'including'),
      ('products_btw', 'Excluding', 'excluding'),
      ('sync_product_changes', 'Yes', '1'),
      ('sync_product_changes', 'No', '0'),
      ('sync_customer_changes', 'Yes', '1'),
      ('sync_customer_changes', 'No', '0'),
      ('sync_product_images', 'Yes', '1'),
      ('sync_product_images', 'No', '0'),
      ('btw_use_country_id', 'Yes', 'true'),
      ('btw_use_country_id', 'No', 'false'),
      ('resync_failed_orders', 'Yes', '1'),
      ('resync_failed_orders', 'No', '0'),
      ('debug_mode', 'Yes', '1'),
      ('debug_mode', 'No', '0'),
      ('enable_products', 'Yes', '1'),
      ('enable_products', 'No', '0'),
      ('enable_customers', 'Yes', '1'),
      ('enable_customers', 'No', '0'),
      ('enable_orders', 'Yes', '1'),
      ('enable_orders', 'No', '0'),
      ('enable_stock', 'Yes', '1'),
      ('enable_stock', 'No', '0'),
      ('enable_delivery', 'Yes', '1'),
      ('enable_delivery', 'No', '0'),
      ('use_verzameldebiteur', 'Yes', '1'),
      ('use_verzameldebiteur', 'No', '0'),
      ('update_guest_debtor', 'Yes', '1'),
      ('update_guest_debtor', 'No', '0'),
      ('create_guest_debtor', 'Yes', '1'),
      ('create_guest_debtor', 'No', '0'),
      ('use_costcenter_costunit', 'Yes', '1'),
      ('use_costcenter_costunit', 'No', '0'),
      ('resync_failed_deliveries', 'Yes', '1'),
      ('resync_failed_deliveries', 'No', '0'),
      ('description_type', 'Order number', 'standaard'),
      ('description_type', 'Order number + Invoice number', 'order_and_invoice'),
      ('description_type', 'Invoice number', 'invoice');

      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_name', 'Description', 'Description');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_name', 'Extra Description', 'ExtraDescription');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_name', 'Note', 'Note');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_name', 'Sku', 'Sku');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_description', 'Description', 'Description');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_description', 'Extra Description', 'ExtraDescription');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_description', 'Note', 'Note');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_short_description', 'Description', 'Description');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_short_description', 'Extra Description', 'ExtraDescription');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_short_description', 'Note', 'Note');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_webshop', 'Yes', '1');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_webshop', 'No', '0');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_sync_images', 'Yes', '1');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('product_sync_images', 'No', '0');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('category_calculation', 'Assortiment', 'assortment');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('category_calculation', 'Artikelgroep', 'item_group');
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('category_calculation', 'Uitschakelen', 'disabled');

      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'priscelistsyncdate', '2001-01-01 00:00:00', '2001-01-01 00:00:00', 'Last Product Sync.', 4, 1, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_pricelist', '0', '2001-01-01 00:00:00', 'synchroniseer prijslijsten', 1, 2, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_classification_codes', 'CLASSCODES TO SYNC', '2001-01-01 00:00:00', 'Classification codes to sync.', 4, 2, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'sync_classification_props', 'CLASSPROPS TO SYNC', '2001-01-01 00:00:00', 'Classification props to sync.', 4, 2, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'group_id_B2B', 'GROUP_ID_B2B', '2001-01-01 00:00:00', 'Groups ID B2B', 4, 2, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/setting')}` (`visible`, `name`, `value`, `timestamp`, `label`, `category_id`, `field_type`, `is_editable_key`, `is_deletable`) VALUES(1, 'not_sync_customer_groups', 'CUST_GROUPS_NOT_TO_SYNC', '2001-01-01 00:00:00', 'Cust. Groups not to sync', 4, 2, 0, 0);
      INSERT INTO `{$installer->getTable('exactonline/option')}` (`setting_key`, `label`, `value`) VALUES ('sync_pricelist', 'Yes', '1'), ('sync_pricelist', 'No', '0');

");


$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'exactonline_debtor_id', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Exact Online ID',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => ''
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'exactonline_debtor_id')
    ->setData('used_in_forms', array('adminhtml_customer'))
    ->save();
/**
$setup->addAttribute('customer_address', 'exactonline_address_id', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Exact Online ID',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => ''
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'exactonline_address_id')
    ->setData('used_in_forms', array('adminhtml_customer_address'))
    ->save();

$setup->updateAttribute('customer','firstname','is_required',0);
$setup->updateAttribute('customer_address','firstname','is_required',0);

$setup->updateAttribute('customer','firstname','validate_rules',NULL);
$setup->updateAttribute('customer_address','firstname','validate_rules',NULL);
*/

$installer->endSetup();