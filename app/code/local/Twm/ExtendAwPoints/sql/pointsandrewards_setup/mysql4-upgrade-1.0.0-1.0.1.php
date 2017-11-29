<?php

$installer = $this;
$installer->startSetup();

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'club_jma')
    ->setData('used_in_forms', array('customer_account_create', 'customer_account_edit', 'adminhtml_customer', 'checkout_register'))
    ->save();

$tableQuote = $this->getTable('sales/quote');

$installer->run(
    "ALTER TABLE $tableQuote ADD `club_jma` integer NOT NULL"
);

$installer->endSetup();