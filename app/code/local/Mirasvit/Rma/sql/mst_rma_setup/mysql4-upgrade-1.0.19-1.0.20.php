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
 * @version   2.4.5
 * @build     1677
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_rma');
if ($version == '1.0.20') {
    return;
} elseif ($version != '1.0.19') {
    die('Please, run migration Rma 1.0.19');
}
$installer->startSetup();
$sql = "
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_resolved`;

ALTER TABLE `{$this->getTable('rma/rule')}` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_resolved`;
";

$helper = Mage::helper('rma/migration');
$helper->trySql($installer, $sql);

$installer->endSetup();
