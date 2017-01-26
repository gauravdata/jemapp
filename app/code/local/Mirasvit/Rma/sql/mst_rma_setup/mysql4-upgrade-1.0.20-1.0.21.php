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
if ($version == '1.0.21') {
    return;
} elseif ($version != '1.0.20') {
    die('Please, run migration Rma 1.0.20');
}
$installer->startSetup();
// @codingStandardsIgnoreStart - SQL requests does not fit with PHP Coding Standards
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rma/offline_order')}` (
    `offline_order_id` int(11) NOT NULL AUTO_INCREMENT,
    `receipt_number` VARCHAR(255) NOT NULL DEFAULT '',
    `customer_id` int(10) unsigned,
        KEY `fk_rma_offline_order_customer_id` (`customer_id`),
    CONSTRAINT `mst_rma_offline_order_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`offline_order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `{$this->getTable('rma/offline_item')}` (
  `offline_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_id` int(11) NOT NULL,
  `offline_order_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `reason_id` int(11) DEFAULT NULL,
  `resolution_id` int(11) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `qty_requested` int(11) DEFAULT NULL,
  `qty_returned` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`offline_item_id`),
  KEY `fk_rma_offline_item_rma_id` (`rma_id`),
  KEY `fk_rma_offline_item_reason_id` (`reason_id`),
  KEY `fk_rma_offline_item_resolution_id` (`resolution_id`),
  KEY `fk_rma_offline_item_condition_id` (`condition_id`),
  KEY `fk_rma_offline_order_id` (`offline_order_id`),
  CONSTRAINT `mst_rma_offline_order_id` FOREIGN KEY (`offline_order_id`) REFERENCES `{$this->getTable('m_rma_offline_order')}` (`offline_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mst_rma_offline_item_condition_id` FOREIGN KEY (`condition_id`) REFERENCES `{$this->getTable('m_rma_condition')}` (`condition_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mst_rma_offline_item_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `{$this->getTable('m_rma_reason')}` (`reason_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mst_rma_offline_item_rma_id` FOREIGN KEY (`rma_id`) REFERENCES `{$this->getTable('m_rma_rma')}` (`rma_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mst_rma_offline_item_resolution_id` FOREIGN KEY (`resolution_id`) REFERENCES `{$this->getTable('m_rma_resolution')}` (`resolution_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
// @codingStandardsIgnoreEnd
$installer->run($sql);


$sql = "
ALTER TABLE `{$this->getTable('rma/item')}` ADD COLUMN `is_removed` TINYINT(1) NOT NULL DEFAULT 0;
";

$helper = Mage::helper('rma/migration');
$helper->trySql($installer, $sql);

$installer->endSetup();
