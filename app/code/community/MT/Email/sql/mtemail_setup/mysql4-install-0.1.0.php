<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('core/email_template');
$table2 = $installer->getTable('mtemail/var');

$installer->run("
ALTER TABLE `{$table}`
	ADD COLUMN `is_mtemail` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Is Mtemail';
");

$installer->run("
CREATE TABLE `{$table2}` (
	`entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`template_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`var_key` VARCHAR(255) NULL DEFAULT NULL,
	`var_value` LONGTEXT NULL,
	`is_system_config` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");

$installer->endSetup();