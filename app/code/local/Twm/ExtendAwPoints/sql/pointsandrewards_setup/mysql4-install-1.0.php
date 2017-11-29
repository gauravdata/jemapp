<?php

$installer = $this;
$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'club_jma', [
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Club JMA Member',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1
]);

$installer->endSetup();