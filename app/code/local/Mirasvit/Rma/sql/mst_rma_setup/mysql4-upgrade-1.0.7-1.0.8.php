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
if ($version == '1.0.8') {
    return;
} elseif ($version != '1.0.7') {
    die("Please, run migration Rma 1.0.7");
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `exchange_order_id` INT(11) ;
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `credit_memo_id` INT(11) ;
ALTER TABLE `{$this->getTable('rma/resolution')}` ADD COLUMN `code` VARCHAR(255) NOT NULL DEFAULT '';
";
$installer->run($sql);

$sql = "
update `{$this->getTable('rma/resolution')}` set code='exchange' where resolution_id = 1 and code='';
update `{$this->getTable('rma/resolution')}` set code='refund' where resolution_id = 2 and code='';
update `{$this->getTable('rma/resolution')}` set code='credit' where resolution_id = 3 and code='';
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();