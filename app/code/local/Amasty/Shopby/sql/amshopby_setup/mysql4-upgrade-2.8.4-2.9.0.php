<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


$this->startSetup();

$table = $this->getTable('amshopby/filter');
if (!$this->getConnection()->tableColumnExists($table, 'disable_seo_url')) {
    $this->run("ALTER TABLE `{$table}` ADD `disable_seo_url` TINYINT(1) NOT NULL DEFAULT '0' AFTER `seo_rel`");
    $this->run("ALTER TABLE `{$table}` ADD INDEX(`disable_seo_url`)");
}
if (!$this->getConnection()->tableColumnExists($table, 'sort_featured_first')) {
    $this->run("ALTER TABLE `{$table}` ADD `sort_featured_first` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sort_by`;");
}
$table = $this->getTable('amshopby/value');
$this->run("ALTER TABLE `{$table}` ADD INDEX(`is_featured`)");

$table = $this->getTable('amshopby/page');
if (!$this->getConnection()->tableColumnExists($table, 'bottom_cms_block_id')) {
    $this->run("ALTER TABLE `{$table}` ADD `bottom_cms_block_id` INT(11) NOT NULL DEFAULT '0' AFTER `cms_block_id`");
}
if (!$this->getConnection()->tableColumnExists($table, 'description')) {
    $this->run("ALTER TABLE `{$table}` ADD `description` TEXT NOT NULL AFTER `title`");
}

$this->endSetup();

