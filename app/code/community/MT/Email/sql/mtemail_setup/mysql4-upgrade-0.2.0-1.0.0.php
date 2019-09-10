<?php

$installer = $this;
$installer->startSetup();
$table2 = $installer->getTable('mtemail/var');

$installer->run("
DROP TABLE IF EXISTS `{$table2}`;
CREATE TABLE `{$table2}` (
	`entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`template_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`block_id` BIGINT(50) NULL DEFAULT NULL,
	`block_name` VARCHAR(50) NULL DEFAULT NULL,
	`var_key` VARCHAR(255) NULL DEFAULT NULL,
	`var_value` LONGTEXT NULL,
	`global` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	`is_system_config` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	`is_default` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	`template_code` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`),
	UNIQUE INDEX `template_id_block_id_block_name_var_key` (`template_id`, `block_id`, `block_name`, `var_key`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");

$fileName = Mage::getBaseDir(). DS .'app' .DS. 'code' .DS. 'community' .DS. 'MT'. DS . 'Email'. DS. 'etc'. DS. 'locale'. DS. 'en_us.xml';
$xmlObj = new Varien_Simplexml_Config($fileName);
$xmlData = $xmlObj->getNode();
$varData = array();
if (count($xmlData->row) > 0) {
    foreach ($xmlData->row as $item) {
        $newVar = Mage::getModel('mtemail/var');
        $newVar->setData(array(
            'block_id' => $item->block_id,
            'block_name' => $item->block_name,
            'var_key' => $item->var_key,
            'var_value' => $item->var_value,
            'global' => $item->global,
            'is_default' => $item->is_default,
            'template_code' => $item->template_code,
        ));
        $newVar->save();
    }
}

@chmod(Mage::getBaseDir(). DS .'media' .DS. 'mt' .DS. 'email' .DS. 'images', 755);

$installer->endSetup();