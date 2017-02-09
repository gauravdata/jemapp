<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   2.4.0
 * @build     1607
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_rma');
if ($version == '1.0.23') {
    return;
} elseif ($version != '1.0.22') {
    die('Please, run migration Rma 1.0.23');
}
$installer->startSetup();

// @codingStandardsIgnoreStart - SQL expressions do not meet PHP standards
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rma/attachment')}` (
    `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
    `email_id` INT(11),
    `comment_id` INT(11) ,
    `external_id` VARCHAR(255) NOT NULL DEFAULT '',
    `storage` VARCHAR(255),
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `size` INT(11) ,
    `body` LONGBLOB,
    KEY `fk_rma_comment_comment_id` (`comment_id`),
    CONSTRAINT `mst_50b78bcead94487f00cb300303cbba5c` FOREIGN KEY (`comment_id`) REFERENCES `{$this->getTable('rma/comment')}` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`attachment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
// @codingStandardsIgnoreEnd
$installer->run($sql);



$installer->endSetup();
