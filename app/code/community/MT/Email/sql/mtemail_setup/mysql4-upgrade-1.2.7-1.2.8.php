<?php

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('mtemail/var');
$resource = Mage::getSingleton('core/resource');
$db = $resource->getConnection('core_write');
$db->addColumn($tableName, 'store_id',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'LENGTH'    => 5,
    'COMMENT'   => 'Store ID',
    'DEFAULT' => 0
));

$db->addColumn($tableName, 'is_tmp',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'LENGTH'    => 1,
    'COMMENT'   => 'Is Temporary',
    'DEFAULT' => 0
));

$tableName = $resource->getTableName('core/email_template');
$db->addColumn($tableName, 'locale',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'LENGTH'    => 10,
    'COMMENT'   => 'Template Locale'
));

$templateCollection = Mage::getModel('core/email_template')->getCollection()
    ->addFieldToFilter('is_mtemail', 1);


$locale = 'en_us';
if ($templateCollection->count() > 0) {

    foreach ($templateCollection as $template) {
        $storeId = $template->getStoreId();
        $template->setLocale($locale)
            ->save();

        $varCollection = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('template_id', $template->getId());

        if ($varCollection->count() > 0) {
            foreach ($varCollection as $var) {
                //assign store id to variable
                $var->setStoreId($storeId)
                    ->save();
            }
        }
    }
}

$installer->endSetup();