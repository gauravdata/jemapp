<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
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

class Pap3Compatibility_Migration_TaskAffiliatesData extends Gpf_Tasks_LongTask {

	private $pap3AffSettings = array();
	private $pap4FormFields = array();

	private $countSignupFields = 0;
	private $countUsers = 0;

	public function __construct() {
		$this->setParams(get_class());
	}

    public function getName() {
        return $this->_('Migrate affiliates');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

	protected function execute() {
		Pap3Compatibility_Migration_OutputWriter::logOnce("Migrating affiliates<br/>");

		if($this->isPending('migrateAffiliatesTaskHeader')) {
			$_SESSION['parentIds'] = array();
			$_SESSION['userIds'] = array();
			$_SESSION['usersForParentIds'] = array();

			$this->deleteAffiliates();
    		$this->setDone();
    	}
    	$time1 = microtime();

    	$this->loadAffiliateSettings();

    	if($this->isPending('migrateAffiliatesTask')) {
    	    try {
    	        $this->migrateAffiliateFields();
    	    } catch (Exception $e) {
    	        Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp; errror migrate affiliate fields: ".$e->getMessage()."<br/>");
    	    }
			$this->migrateInitialPayoutSetting();
    		$this->setDone();
   		} else {
	   		$this->loadAffiliateFields();
   		}

   		$this->migrateAffiliates();

    	$time2 = microtime();
		Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }

    private function loadAffiliateSettings() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('code', 'code');
        $selectBuilder->select->add('value', 'value');

        $selectBuilder->from->add('wd_g_settings');

        $selectBuilder->where->add('code', 'LIKE', 'Aff_signup_%');
        $selectBuilder->where->add('code', '=', 'Aff_initial_min_payout', 'OR');

        $result = new Gpf_Data_RecordSet();
        $result->load($selectBuilder);

        $count = 0;
        foreach($result as $record) {
			$this->pap3AffSettings[$record->get('code')] = $record->get('value');
        	$count++;
        }
    }

    private function getPap3Setting($code) {
    	if(!isset($this->pap3AffSettings[$code])) {
    		return '';
    	}
    	return $this->pap3AffSettings[$code];
    }

    private function migrateAffiliateFields() {
    	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Migrating affiliate fields.....");

    	$this->insertSignupField('street');
    	$this->insertSignupField('city');
    	$this->insertSignupField('company_name');
    	$this->insertSignupField('state');
    	$this->insertSignupField('zipcode');
    	$this->insertSignupField('weburl');
    	$this->insertSignupField('phone');
    	$this->insertSignupField('fax');
    	$this->insertSignupField('tax_ssn');
    	$this->insertSignupField('country');
    	$this->insertSignupField('data1');
    	$this->insertSignupField('data2');
    	$this->insertSignupField('data3');
    	$this->insertSignupField('data4');
    	$this->insertSignupField('data5');

    	while($this->countSignupFields < 25) {
    		$this->insertEmptySignupField();
    	}

       	Pap3Compatibility_Migration_OutputWriter::log(" (".$this->countSignupFields.") ..... DONE<br/>");
    }

    private function insertSignupField($constantName, $realInsert = true) {
    	$fieldHide = $this->getPap3Setting('Aff_signup_'.$constantName);

        if($fieldHide == '' || $fieldHide == '0') {
        	return;
        }

    	$fieldMandatory = $this->getPap3Setting('Aff_signup_'.$constantName.'_mandatory');
    	if($fieldMandatory == '') {
    		$fieldMandatory = 'false';
    	}

    	if(strpos($constantName, 'data') !== false) {
    		$fieldName = $this->getPap3Setting('Aff_signup_'.$constantName.'_name');
    		if($fieldName == '') {
    			return;
    		}
    		$fieldName = ucfirst($fieldName);
    	} else {
    		$fieldName = $constantName;
    		$fieldName = str_replace('_', ' ', $fieldName);
    		$fieldName = ucfirst($fieldName);
    	}

    	$fieldType = 'T';
    	if(strpos($constantName, 'country') !== false) {
    		$fieldType = 'C';
    	}

    	$this->countSignupFields++;

    	if($realInsert) {
    		try {
    			$obj = new Gpf_Db_FormField();
    			$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
    			$obj->setFormId('affiliateForm');
    			$obj->setCode('data'.$this->countSignupFields);
    			$obj->setName($fieldName);
    			$obj->setType($fieldType);
    			$obj->setStatus($this->translateFieldStatus($fieldMandatory));
    			$obj->save();
    		} catch(Exception $e) {
    			Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Minor errror: ".$e->getMessage()."<br/>");
    		}
    	}

    	$this->pap4FormFields[$constantName] = 'data'.$this->countSignupFields;
    }

	private function loadAffiliateFields() {
		$this->pap4FormFields = array();

    	$this->insertSignupField('street', false);
    	$this->insertSignupField('city', false);
    	$this->insertSignupField('company_name', false);
    	$this->insertSignupField('state', false);
    	$this->insertSignupField('zipcode', false);
    	$this->insertSignupField('weburl', false);
    	$this->insertSignupField('phone', false);
    	$this->insertSignupField('fax', false);
    	$this->insertSignupField('tax_ssn', false);
    	$this->insertSignupField('country', false);
    	$this->insertSignupField('data1', false);
    	$this->insertSignupField('data2', false);
    	$this->insertSignupField('data3', false);
    	$this->insertSignupField('data4', false);
    	$this->insertSignupField('data5', false);
	}

    private function insertEmptySignupField() {
    	$this->countSignupFields++;

    	$obj = new Gpf_Db_FormField();
    	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
    	$obj->setFormId('affiliateForm');
    	$obj->setCode('data'.$this->countSignupFields);
    	$obj->setName('Unused');
    	$obj->setType('T');
    	$obj->setStatus('D');
    	$obj->save();
    }

    private function translateFieldStatus($mandatory) {
    	if($mandatory == 'true') {
    		return 'M';
    	} else {
    		return 'O';
    	}
    }

    private function migrateAffiliates() {
    	$task = new Pap3Compatibility_Migration_TaskAffiliates("affiliates", false, $this->pap4FormFields);
    	$task->run();
    }

	private function deleteAffiliates() {
		Pap3Compatibility_Migration_OutputWriter::logOnce("&nbsp;&nbsp;Deleting affiliates .....");
		// pap users
		$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add(Pap_Db_Table_Users::getName());
    	$sql->where->add('rtype', '=', 'A');
    	$sql->execute();

    	// g_users
    	$inSelect = new Gpf_SqlBuilder_SelectBuilder();
    	$inSelect->select->add(Pap_Db_Table_Users::ACCOUNTUSERID);
    	$inSelect->from->add(Pap_Db_Table_Users::getName());
		$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add(Gpf_Db_Table_Users::getName());
    	$sql->where->add('accountuserid', 'NOT IN', $inSelect, 'AND', false);
    	$sql->execute();

    	// g_authusers
    	$inSelect = new Gpf_SqlBuilder_SelectBuilder();
        $inSelect->select->add(Gpf_Db_Table_Users::AUTHID);
        $inSelect->from->add(Gpf_Db_Table_Users::getName());
		$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add(Gpf_Db_Table_AuthUsers::getName());
    	$sql->where->add('authid', 'NOT IN', $inSelect, 'AND', false);
    	$sql->execute();

    	// g_userattributes
    	$inSelect = new Gpf_SqlBuilder_SelectBuilder();
        $inSelect->select->add(Gpf_Db_Table_Users::ID);
        $inSelect->from->add(Gpf_Db_Table_Users::getName());
		$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add(Gpf_Db_Table_UserAttributes::getName());
    	$sql->where->add('accountuserid', 'NOT IN', $inSelect, 'AND', false);
    	$sql->execute();

    	// g_gadgets
    	$inSelect = new Gpf_SqlBuilder_SelectBuilder();
        $inSelect->select->add(Gpf_Db_Table_Users::ID);
        $inSelect->from->add(Gpf_Db_Table_Users::getName());
		$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add(Gpf_Db_Table_Gadgets::getName());
    	$sql->where->add('accountuserid', 'NOT IN', $inSelect, 'AND', false);
    	$sql->execute();

    	Pap3Compatibility_Migration_OutputWriter::log("DONE<br/>");
	}

    private function migrateInitialPayoutSetting() {
        echo "&nbsp;&nbsp;Migrating initial payout setting.....";

        if ($this->getPap3Setting('Aff_initial_min_payout') != '') {
            Gpf_Settings::set(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME,
                $this->getPap3Setting('Aff_initial_min_payout'));
        }

        echo "DONE<br/>";
    }
}
?>
