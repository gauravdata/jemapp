<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */

$installer = $this;
 
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('translationhelper/translation'), 'is_imported',
        'tinyint(1) NOT NULL AFTER `locale`');
$installer->getConnection()->addColumn($installer->getTable('translationhelper/translation'), 'is_missing',
        'tinyint(1) NOT NULL AFTER `is_imported`');
$installer->getConnection()->addColumn($installer->getTable('translationhelper/translation'), 'is_hidden',
        'tinyint(1) NOT NULL AFTER `is_missing`');
 
$installer->endSetup();