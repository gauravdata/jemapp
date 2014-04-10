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
 * @package    AW_Customsmtp
 * @version    1.0.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('customsmtp/mail')} (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `subject` varchar(255) NOT NULL,
        `is_plain` tinyint(1) NOT NULL DEFAULT '0',
        `body` text NOT NULL,
        `from_email` varchar(255) NOT NULL,
        `from_name` varchar(255) NOT NULL,
        `to_email` varchar(255) NOT NULL,
        `reply_to` varchar(255) NOT NULL,
        `to_name` varchar(255) NOT NULL,
        `date` datetime DEFAULT NULL,
        `status` enum('failed','pending','processed','in_progress') NOT NULL DEFAULT 'processed',
        `template_id` varchar(255) NOT NULL,
        `store_id` int(4) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `subject` (`subject`),
        KEY `is_plain` (`is_plain`),
        KEY `from_email` (`from_email`),
        KEY `from_name` (`from_name`),
        KEY `to_email` (`to_email`),
        KEY `to_name` (`to_name`),
        KEY `date` (`date`),
        KEY `status` (`status`),
        KEY `template_id` (`template_id`,`store_id`),
        KEY `reply_to` (`reply_to`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->endSetup();