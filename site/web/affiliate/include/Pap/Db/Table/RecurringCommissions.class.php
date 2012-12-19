<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Db_Table_RecurringCommissions extends Gpf_DbEngine_Table {
	const ID = 'recurringcommissionid';
	const TRANSACTION_ID = 'transid';
	const RECURRENCE_PRESET_ID = 'recurrencepresetid';
	const COMMISSION_TYPE_ID = 'commtypeid';
	const STATUS = 'rstatus';
	const LAST_COMMISSION_DATE = 'lastcommissiondate';

	private static $instance;
	    
	/**
	 * @return Pap_Db_Table_RecurringCommissions
	 */
	public static function getInstance() {
	    if(self::$instance === null) {
	        self::$instance = new self;
	    }
	    return self::$instance;
	}
	    
	protected function initName() {
	    $this->setName('pap_recurringcommissions');
	}
    
	public static function getName() {
        return self::getInstance()->name();
    }
	
	protected function initColumns() {
		$this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
		$this->createColumn(self::TRANSACTION_ID, self::CHAR, 8);
		$this->createColumn(self::RECURRENCE_PRESET_ID, self::CHAR, 8);
		$this->createColumn(self::COMMISSION_TYPE_ID, self::CHAR, 8);
		$this->createColumn(self::STATUS, self::CHAR, 1);
		$this->createColumn(self::LAST_COMMISSION_DATE, self::DATETIME, 1);
	}
}
?>
