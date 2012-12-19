<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: UserInCommissionGroup.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Db_Table_UserPayoutOptions extends Gpf_DbEngine_Table {
	const USERID = 'userid';
    const VALUE = 'value';
	const FORMFIELDID = 'formfieldid';

	private static $instance;

	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
    
	/**
	 * @return array<Pap_Db_UserPayoutOption>
	 */
	public function getValues($formName, $userId) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		
		$select->select->add('ff.'.Gpf_Db_Table_FormFields::ID, Gpf_Db_Table_FormFields::ID);
		$select->select->add('ff.'.Gpf_Db_Table_FormFields::CODE, Gpf_Db_Table_FormFields::CODE);
		$select->select->add('upo.'.self::VALUE, self::VALUE);
		$select->select->add('upo.'.self::USERID, self::USERID);
		
		$select->from->add(Gpf_Db_Table_FormFields::getName(), 'ff');
		$select->from->addInnerJoin(self::getName(), 'upo', 'upo.formfieldid=ff.formfieldid');
		
		$select->where->add('upo.'.self::USERID, '=', $userId);
		$select->where->add('ff.'.Gpf_Db_Table_FormFields::FORMID, '=', $formName);
		
		$values = array();
		foreach ($select->getAllRowsIterator() as $row) {
		    $payoutOption = new Pap_Db_UserPayoutOption();
		    $payoutOption->fillFromRecord($row);
		    $values[$row->get('code')] = $payoutOption;
		}
		return $values;
	}

	protected function initName() {
		$this->setName('pap_userpayoutoptions');
	}

	public static function getName() {
		return self::getInstance()->name();
	}

	protected function initColumns() {
		$this->createColumn(self::USERID, 'char', 8, true);
		$this->createColumn(self::FORMFIELDID, 'int', true);
		$this->createColumn(self::VALUE, 'text');
	}

}
?>
