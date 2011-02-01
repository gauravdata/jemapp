<?php

/**
 * Workspace
 *
 * @author jeroen
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class Admin_Data_DbTable_Account extends Admin_Data_DbTable_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'account';
	protected $_primary = 'account_id';

}