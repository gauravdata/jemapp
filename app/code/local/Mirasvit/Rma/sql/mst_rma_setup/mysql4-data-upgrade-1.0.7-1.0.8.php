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
$connection = $installer->getConnection();

$sql = "
update `{$this->getTable('rma/resolution')}` set code='exchange' where resolution_id = 1 and code='';
update `{$this->getTable('rma/resolution')}` set code='refund' where resolution_id = 2 and code='';
update `{$this->getTable('rma/resolution')}` set code='credit' where resolution_id = 3 and code='';
";
$installer->run($sql);
