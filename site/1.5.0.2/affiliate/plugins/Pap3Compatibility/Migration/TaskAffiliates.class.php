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

class Pap3Compatibility_Migration_TaskAffiliates extends Pap3Compatibility_Migration_LongTask {

	private $pap3PayoutFields;
	
	public function getCount() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('count(userid)', 'count');
        		
        $result = $selectBuilder->getAllRows();
        foreach($result as $record) {
        	return $record->get('count');        
        }
        
        return 0;			
	}

	public function getSelect() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
     
        $selectBuilder->from->add('wd_g_users', 'u');
        $selectBuilder->where->add('u.rtype', '=', '4');
        $selectBuilder->where->add('u.deleted', '=', '0');
        $selectBuilder->orderBy->add('u.dateinserted');
        $selectBuilder->orderBy->add('u.userid');
           	
        return $selectBuilder;
	}
	
	public function getFullSelect() {
    	$selectBuilder = $this->getSelect();
		$selectBuilder->select->add('u.userid', 'userid');
        $selectBuilder->select->add('u.accountid', 'accountid');
        $selectBuilder->select->add('u.refid', 'refid');
        $selectBuilder->select->add('u.username', 'username');
        $selectBuilder->select->add('u.rpassword', 'rpassword');
        $selectBuilder->select->add('u.name', 'name');
        $selectBuilder->select->add('u.surname', 'surname');
        $selectBuilder->select->add('u.rstatus', 'rstatus');
        $selectBuilder->select->add('u.product', 'product');
        $selectBuilder->select->add('u.dateinserted', 'dateinserted');
        $selectBuilder->select->add('u.dateapproved', 'dateapproved');
        $selectBuilder->select->add('u.deleted', 'deleted');
        $selectBuilder->select->add('u.userprofileid', 'userprofileid');
        $selectBuilder->select->add('u.rtype', 'rtype');
        $selectBuilder->select->add('u.parentuserid', 'parentuserid');
        $selectBuilder->select->add('u.leftnumber', 'leftnumber');
        $selectBuilder->select->add('u.rightnumber', 'rightnumber');
        $selectBuilder->select->add('u.company_name', 'company_name');
        $selectBuilder->select->add('u.weburl', 'weburl');
        $selectBuilder->select->add('u.street', 'street');
        $selectBuilder->select->add('u.city', 'city');
        $selectBuilder->select->add('u.state', 'state');
        $selectBuilder->select->add('u.country', 'country');
        $selectBuilder->select->add('u.zipcode', 'zipcode');
        $selectBuilder->select->add('u.phone', 'phone');
        $selectBuilder->select->add('u.fax', 'fax');
        $selectBuilder->select->add('u.tax_ssn', 'tax_ssn');
        $selectBuilder->select->add('u.data1', 'data1');
        $selectBuilder->select->add('u.data2', 'data2');
        $selectBuilder->select->add('u.data3', 'data3');
        $selectBuilder->select->add('u.data4', 'data4');
        $selectBuilder->select->add('u.data5', 'data5');
        $selectBuilder->select->add('u.payoptid', 'payoptid');
        $selectBuilder->select->add('u.originalparentid', 'originalparentid');
        $selectBuilder->select->add('u.flags', 'flags');
		$selectBuilder->from->addLeftJoin('wd_g_settings', 's', 'u.userid=s.userid AND s.code=\'Aff_min_payout\'');		
        $selectBuilder->select->add('s.value', 'minimumpayout');
        
        return $selectBuilder;
	}	
		
    public function processRecord($record) {
        try {
        	$this->insertUser($record);
        } catch(Gpf_Exception $e) {
        	Pap3Compatibility_Migration_OutputWriter::log("<br/>Exception when inserting record ".$e->getMessage());
        }
    }
        
    private function insertUser($record) {
    	$user = new Pap_Affiliates_User();
    	$user->setId($record->get('userid'));
    	$user->setRefId(($record->get('refid') != '' ? $record->get('refid') : $record->get('userid')));
    	$user->setPassword($record->get('rpassword'));
    	$user->setUserName($record->get('username'));
    	$user->setFirstName($record->get('name'));
    	$user->setLastName($record->get('surname'));
    	$user->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
    	$user->setDateInserted($record->get('dateinserted'));
    	if ($record->get('minimumpayout') != NULL) {
    		$user->setMinimumPayout($record->get('minimumpayout'));
    	} else {
    		$user->setMinimumPayout(Gpf_Settings::get(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME));
    	}
    	if($record->get('dateapproved') != null && $record->get('dateapproved') != '') {
    		$user->setDateApproved($record->get('dateapproved'));
    	}
    	if (Pap3Compatibility_Migration_Pap3Constants::translateStatus($record->get('rstatus')) == Pap_Common_Constants::STATUS_APPROVED && 
    	$user->getDateApproved() == null) {
    		$actualDate = new Gpf_DateTime();
    		$user->setDateApproved($actualDate->toDateTime());
    	}
    	$user->setStatus(Pap3Compatibility_Migration_Pap3Constants::translateStatus($record->get('rstatus')));
    	$user->setType('A');
        $user->set('numberuserid',1);
        
    	$this->setAffiliateField($user, $record, 'street');
    	$this->setAffiliateField($user, $record, 'city');
    	$this->setAffiliateField($user, $record, 'company_name');
    	$this->setAffiliateField($user, $record, 'state');
    	$this->setAffiliateField($user, $record, 'zipcode');
    	$this->setAffiliateField($user, $record, 'weburl');
    	$this->setAffiliateField($user, $record, 'phone');
    	$this->setAffiliateField($user, $record, 'fax');
    	$this->setAffiliateField($user, $record, 'tax_ssn');
    	$this->setAffiliateField($user, $record, 'country');
    	$this->setAffiliateField($user, $record, 'data1');
    	$this->setAffiliateField($user, $record, 'data2');
    	$this->setAffiliateField($user, $record, 'data3');
    	$this->setAffiliateField($user, $record, 'data4');
    	$this->setAffiliateField($user, $record, 'data5');

    	$user->setSendNotification(false);
    	$user->setPayoutOptionId($this->savePayoutData($record->get('userid')));
    	$user->save();
    	
    	// handle parent id
        $parentUserId = $record->get('parentuserid');
        if($parentUserId != '') {
            $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
            $updateBuilder->from->add(Pap_Db_Table_Users::getName());
            $updateBuilder->set->add(Pap_Db_Table_Users::PARENTUSERID, $parentUserId);
            $updateBuilder->where->add(Pap_Db_Table_Users::ID, '=', $record->get('userid'));
            try {
                $updateBuilder->executeOne();
            } catch (Gpf_Exception $e) {
                Pap3Compatibility_Migration_OutputWriter::log("<br/>Error setting parentuserid: ".$e->getMessage());
            }
        }
        
    	
    	$this->countUsers++;
    }
    
    private function setAffiliateField($userObj, $record, $code) {
    	$value = $record->get($code);
    	if($value == '') {
    		return;
    	}
    	if (!array_key_exists($code, $this->param)) {
    	    return;
    	}
    	$userObj->set($this->param[$code], $value);
    }
    
    public function afterAllRecordsProcessed() {
    	$this->processParentUsers();
    }
    
	/**
	 * special handling for parent user IDs that were not set during insert
	 *
	 */
    private function processParentUsers() {        
		Pap3Compatibility_Migration_OutputWriter::log("<br/>Fixing parent users.....");
		
		$updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
		$updateBuilder->from->add(Pap_Db_Table_Users::getName(), 'u');
		$updateBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(), 'pu', 'u.parentuserid = pu.userid');
		$updateBuilder->set->add('u.parentuserid' ,'NULL', false);
		$updateBuilder->where->add('u.parentuserid', 'is not', 'NULL', 'AND', false);
		$updateBuilder->where->add('pu.userid', 'is', 'NULL', 'AND', false);		
		try {
            $statement = $updateBuilder->execute();
		} catch (Gpf_Exception $e) {
		    Pap3Compatibility_Migration_OutputWriter::log("<br/>&nbsp;&nbsp;Exception when updating parentuserids: ".$e->getMessage());
		    return;
		}
		
		Pap3Compatibility_Migration_OutputWriter::log("<br/>&nbsp;&nbsp;".$statement->affectedRows()." parentuserids fixed");
    }
    
    /**
     * 
     * @param $userID
     * @return Gpf_Data_IndexedRecordSet
     */
    private function getPap3UserPayoutData($userID) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('code', 'code');
        $selectBuilder->select->add('value', 'value');
        $selectBuilder->from->add('wd_g_settings');
        $selectBuilder->where->add('userid', '=', $userID);
        $selectBuilder->where->add('id2', '=', null);         
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition(); 
        $condition->add('code', 'LIKE', 'Aff_payoptionfield_%', 'OR');
        $condition->add('code', '=', 'Aff_payout_type', 'OR');
        $selectBuilder->where->addCondition($condition);
        
        $pap3PayoutSettings = new Gpf_Data_IndexedRecordSet('code');
        $pap3PayoutSettings->load($selectBuilder);
        return $pap3PayoutSettings;
    }
    
    private function savePayoutData($userID) {
    	$pap3PayoutSettings = $this->getPap3UserPayoutData($userID);
    	
    	try {
    		$payoutOptionId = $pap3PayoutSettings->getRecord('Aff_payout_type')->get('value');	
    	} catch (Gpf_Data_RecordSetNoRowException $e) {
    		return Gpf_DbEngine_Row::NULL;	
    	}
        
        $formFields = Gpf_Db_Table_FormFields::getInstance();
        $formName = Pap_Merchants_User_AffiliateForm::PAYOUT_OPTION . $payoutOptionId;
        $payoutOptionFields = $formFields->getFieldsNoRpc($formName);
        
        foreach ($payoutOptionFields as $field) {
            $payoutOptionUserValue = new Pap_Db_UserPayoutOption();
            $payoutOptionUserValue->setUserId($userID);
            $payoutOptionUserValue->setFormFieldId($field->get("id"));
            try {
            	$payoutOptionValue = $pap3PayoutSettings->getRecord('Aff_payoptionfield_' . 
            	   $this->getFieldCode($field->get("code"), $payoutOptionId))->get('value');
            } catch (Gpf_Exception $e) {
            	$payoutOptionValue = '';
            }
            $payoutOptionUserValue->setValue($payoutOptionValue);
            $payoutOptionUserValue->save();
        }
        
        return $payoutOptionId;
    }
    
    public function beforeRecordsProcessed() {
        $this->loadPayoutFields();
    }
    
    private function loadPayoutFields() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
    	$selectBuilder->select->add('payfieldid');
    	$selectBuilder->select->add('code');
    	$selectBuilder->select->add('payoptid');
    	$selectBuilder->from->add('wd_pa_payoutfields');
        
    	$this->pap3PayoutFields = array();
    	foreach ($selectBuilder->getAllRowsIterator() as $payoutField) {
    		$this->pap3PayoutFields[$payoutField->get('code') . '_' . $payoutField->get('payoptid')] = $payoutField->get('payfieldid');
    	}
    }
    
    private function getFieldCode($code, $payoutOptionID) {
    	$name = $code . '_' . $payoutOptionID;
        if (!isset($this->pap3PayoutFields[$name])) {
        	throw new Gpf_Exception('Undefined payout field');
        }
        return $this->pap3PayoutFields[$name];
    }
}
?>
