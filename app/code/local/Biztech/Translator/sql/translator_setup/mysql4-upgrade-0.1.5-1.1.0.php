<?php
$installer = $this;

$installer->startSetup();

// Add to translate
$attributeName = 'Product Translated'; // Name of the attribute
$attributeCode = 'translated'; //Code of the attribute
$attributeGroup = 'General';          // Group to add the attribute to
$attributeSetIds = array(Mage::getModel('catalog/product')->getDefaultAttributeSetId());         // Array with attribute set ID's to add this attribute to. (ID:4 is the Default Attribute Set)

$data = array(
    'type' => 'int',                                                           //Attribute type
    'input' => 'boolean',                                                        // Input type
    //'option'     => array ('values' => array(0 => 'Yes',1 => 'No')),
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    // Attribute scope
    'required' => false,                                                       // Is this attribute required?
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'used_in_product_listing' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'label' => $attributeName
);
$installer->endSetup();
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();
$installer->addAttribute('catalog_product', $attributeCode, $data);

foreach ($attributeSetIds as $attributeSetId) {
    $installer->addAttributeToGroup('catalog_product', $attributeSetId, $attributeGroup, $attributeCode);
}

if (!$installer->tableExists('translator_cron')) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('translator_cron'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Store id')
        ->addColumn('cron_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        ), 'Date and Time of Cron RUN')
        ->addColumn('update_cron_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        ), 'Date and Time of Cron RUN')
        ->addColumn('cron_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Cron Name')
        ->addColumn('product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Product Ids selected to translate')
        ->addColumn('lang_from', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(), 'Language From')
        ->addColumn('lang_to', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(), 'Language to')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'abort,pending,processing,success')
        ->addColumn('is_abort', Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
            'nullable' => true,
            'default' => 0
        ), 'is aborted')
        ->setComment('manage translate cron');

    $installer->getConnection()->createTable($table);
}

if (!$installer->tableExists('translator_logcron')) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('translator_logcron'))
        ->addColumn('trans_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ), 'Id')
        ->addColumn('cron_job_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
        ), 'Cron JOB CODE')
        ->addColumn('cron_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        ), 'Date and Time of Cron RUN')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'default' => 0
        ), 'Status 0 => failed, 1 => success, 2 => abort')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'default' => 0
        ), 'Store Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Last Translated Product Id')
        ->addColumn('remain_limit', Varien_Db_Ddl_Table::TYPE_TEXT, '5', array(
            'nullable' => true,
            'default' => 0
        ), 'Remaining Daily Limit')
        /*->addIndex(
            $installer->getIdxName(
                'translator/logcron',
                array(
                    'cron_job_code',
                    'cron_date',
                ),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array(
                'cron_date',
                'cron_job_code',
            ),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )*/
        ->setComment('manage translate cron log');

    $installer->getConnection()->createTable($table);
}


$installer->endSetup();

