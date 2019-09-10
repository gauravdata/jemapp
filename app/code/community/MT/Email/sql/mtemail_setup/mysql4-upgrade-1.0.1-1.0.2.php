<?php

$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$tableName = $resource->getTableName('core/email_template');
$db = $resource->getConnection('core_write');
$db->addColumn($tableName, 'store_id',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'LENGTH'    => 5,
    'COMMENT'   => 'Store ID',
    'DEFAULT' => 0
));

$installer->endSetup();