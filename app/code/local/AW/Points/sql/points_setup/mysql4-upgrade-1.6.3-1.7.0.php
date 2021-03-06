<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Points
 * @version    1.9.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

$installer = $this;

$installer->startSetup();
if (!$installer->tableExists($this->getTable('points/invitation'))) {
    $oldInvitationTableName = (string)Mage::getConfig()->getTablePrefix() . 'aw_points_invitatioin';
    $installer->run("
        RENAME TABLE {$oldInvitationTableName} TO {$installer->getTable('points/invitation')};
    ");
}
$installer->run("
    ALTER TABLE {$this->getTable('points/invitation')} DROP INDEX `email`;
");
$installer->endSetup();