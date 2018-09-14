<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
//RE-NAME FIELDS FROM PREVIOUS VERSION
$installer->run("
ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` CHANGE `swatchattribute_id` `entity_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` CHANGE `swatch_status` `is_enabled` tinyint(4) NOT NULL;
ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` CHANGE `display_popup` `is_display_popup` tinyint(4) NOT NULL;
ALTER TABLE `{$this->getTable('awcolorswatches/swatch')}` MODIFY `option_id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `{$this->getTable('awcolorswatches/swatch')}` ADD CONSTRAINT `FK_option_id` FOREIGN KEY (`option_id`) REFERENCES {$this->getTable('eav/attribute_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` ADD `attribute_id` smallint(5) UNSIGNED NOT NULL;
ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` ADD CONSTRAINT `FK_attribute_id` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav/attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

//START DATA IMPORT attribute_code -> attribute_id
$collection = Mage::getModel('awcolorswatches/swatchattribute')->getCollection();
foreach ($collection as $item) {
    $attributeCode = $item->getData('attribute_code');
    $attribute = Mage::getModel('eav/entity_attribute')->load($attributeCode, 'attribute_code');
    $item->setData('attribute_id', $attribute->getId());
    try {
        $item->save();
    } catch (Exception $e) {
        Mage::logException($e);
    }
}
// END DATA IMPORT. NOW WE CAN REMOVE USELESS FIELD  attribute_code
$installer->run("
    ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` DROP COLUMN `attribute_code`;
");

//ADD NEW FIELDS
$installer->run("
    ALTER TABLE `{$this->getTable('awcolorswatches/swatchattribute')}` ADD `is_override_with_child` tinyint(4) NOT NULL;
");
$installer->endSetup();