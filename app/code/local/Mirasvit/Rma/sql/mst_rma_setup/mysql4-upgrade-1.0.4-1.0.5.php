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
if ($version == '1.0.5') {
    return;
} elseif ($version != '1.0.4') {
    die("Please, run migration Rma 1.0.4");
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/status')}` ADD COLUMN `code` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `last_reply_name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `last_reply_at` TIMESTAMP NULL;
";
$installer->run($sql);

$sql = "
   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,code,is_rma_resolved,customer_message,history_message,admin_message) VALUES ('Package Sent','1','25','package_sent','0','','','Package is sent.');

";
$installer->run($sql);

/**                                    **/


$installer->endSetup();