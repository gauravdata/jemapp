<?php
$this->startSetup();

$this->run("
    alter table `{$this->getTable('translator_cron')}` change column product_ids  `product_ids` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Product Ids selected to translate';
");

$this->endSetup();