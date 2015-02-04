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
 * @version   1.0.7
 * @build     658
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_rma');
if ($version == '1.0.3') {
    return;
} elseif ($version != '1.0.2') {
    die("Please, run migration Rma 1.0.2");
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/item')}` ADD COLUMN `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/item')}` ADD COLUMN `product_options` TEXT;
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();