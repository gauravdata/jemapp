<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Account.class.php 27612 2010-03-23 13:24:47Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliate
 */
class Pap_Account extends Gpf_Db_Account {
	
	private $oldStatus;

    public function getCreateTask() {
        $task = new Pap_Install_CreateAccountTask();
        $task->setAccount($this);
        return $task;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     * @return Pap_Account
     */
    public static function getAccount($accountId) {
        $account = new Pap_Account();
        $account->setId($accountId);
        $account->load();
        return $account;
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @return Pap_Common_User
     */
    public function getMerchant() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add('pu.'.Pap_Db_Table_Users::ID, Pap_Db_Table_Users::ID);
		$select->from->add(Pap_Db_Table_Users::getName(), 'pu');
		$select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu',
		'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=gu.'.Gpf_Db_Table_Users::ID);
		$select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
		'au.'.Gpf_Db_Table_AuthUsers::ID.'=gu.'.Gpf_Db_Table_Users::AUTHID);
		$select->where->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, '=', $this->getEmail());
		$select->where->add('gu.'.Gpf_Db_Table_Users::ACCOUNTID, '=', $this->getId());
		$merchantId = $select->getOneRow()->get(Pap_Db_Table_Users::ID);
		
		return Pap_Merchants_User::getUserById($merchantId);
    }

    protected function afterLoad() {
        parent::afterLoad();
        $this->oldStatus = $this->getStatus();
    }

    protected function afterSave() {
        if ($this->oldStatus == $this->getStatus()) {
            return;
        }
        $this->onStatusChange();
    }

    /**
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function insert() {
        parent::insert();
        $this->afterSave();
    }

    /**
     * @param array $updateColumns list of columns that should be updated. if not set, all modified columns are update
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function update($updateColumns = array()) {
        parent::update($updateColumns);
        $this->afterSave();
    }

    protected function onStatusChange() {
    	Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Account.onStatusChange', $this);
    }
}
?>
