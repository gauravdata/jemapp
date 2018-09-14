<?php

$this->startSetup();
try {
    $this->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('awcolorswatches/swatchattribute')}` (
        `swatchattribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `swatch_status` tinyint(4) NOT NULL,
        `display_popup` tinyint(4) NOT NULL,
        `attribute_code` varchar(255) NOT NULL,
        PRIMARY KEY (`swatchattribute_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

        CREATE TABLE IF NOT EXISTS `{$this->getTable('awcolorswatches/swatch')}` (
        `swatch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `option_id` int(10) NOT NULL,
        `image` text NOT NULL,
        PRIMARY KEY (`swatch_id`),
        KEY `option_id` (`option_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    ");
} catch (Exception $ex) {
    Mage::logException($ex);
}
$this->endSetup();