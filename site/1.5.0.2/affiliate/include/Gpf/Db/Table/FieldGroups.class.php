<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: PayoutOptions.class.php 18660 2008-06-19 15:30:59Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_Table_FieldGroups extends Gpf_DbEngine_Table {
	const ID = "fieldgroupid";
	const ACCOUNTID = "accountid";
	const TYPE = "rtype";
	const STATUS = "rstatus";
	const ORDER = "rorder";
	const NAME = "name";
	const DATA1 = "data1";
	const DATA2 = "data2";
	const DATA3 = "data3";
	const DATA4 = "data4";
	const DATA5 = "data5";
	
	private static $instance;
	    
	public static function getInstance() {
	    if(self::$instance === null) {
	        self::$instance = new self;
	    }
	    return self::$instance;
	}
	    
	protected function initName() {
	    $this->setName('g_fieldgroups');
	}
    
	public static function getName() {
        return self::getInstance()->name();
    }
	
	function initColumns() {
		$this->createPrimaryColumn(self::ID, 'char', 8, true);
		$this->createColumn(self::ACCOUNTID, 'char', 8);
		$this->createColumn(self::TYPE, 'char', 1);
		$this->createColumn(self::STATUS, 'char', 1);
		$this->createColumn(self::ORDER, 'int');
		$this->createColumn(self::NAME, 'char', 100);
		$this->createColumn(self::DATA1, 'char');
		$this->createColumn(self::DATA2, 'char');
		$this->createColumn(self::DATA3, 'char');
		$this->createColumn(self::DATA4, 'char');
		$this->createColumn(self::DATA5, 'char');
	}
	
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::NAME, self::TYPE, self::ACCOUNTID)));
    }
}

?>
