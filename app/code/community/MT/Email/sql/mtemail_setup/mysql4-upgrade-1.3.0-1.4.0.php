<?php

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('core/email_template');
$resource = Mage::getSingleton('core/resource');
$db = $resource->getConnection('core_write');

$db->addColumn($tableName, 'template_plain_text',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'COMMENT'   => 'Plain Text Email Version'
));

$installer->endSetup();