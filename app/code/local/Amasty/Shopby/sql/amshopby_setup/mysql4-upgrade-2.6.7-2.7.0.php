<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

function alterPageMultipleStores($setup)
{
    /**
     * @Migration field_exist:amshopby/page|stores:1
     * @Migration field_exist:amshopby/page|store_id:0
     */
    $table = $setup->getTable('amshopby/page');

    if (!$setup->getConnection()->tableColumnExists($table, 'stores')) {
        $setup->run("ALTER TABLE `{$table}` ADD `stores` TEXT NOT NULL AFTER `page_id`");
    }
}

function enlargeValueMultistoreFields($setup)
{
    $table = $setup->getTable('amshopby/value');

    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `title` `title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_title` `meta_title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_descr` `meta_descr` TEXT");
}

alterPageMultipleStores($this);
enlargeValueMultistoreFields($this);

$this->endSetup();
