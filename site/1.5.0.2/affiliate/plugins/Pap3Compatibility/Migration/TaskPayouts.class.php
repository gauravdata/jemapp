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

class Pap3Compatibility_Migration_TaskPayouts extends Gpf_Object {
    
	private $payoutHistoryIds = array();
	
    public function run() {
    	echo "Migrating payouts<br/>";
   	
    	$time1 = microtime();
    	
    	try {
    		$this->migratePayoutOptions();
    		$this->migratePayoutOptionFields();
    		$this->migratePayoutHistory();
    		$this->migratePayouts();
    	} catch(Exception $e) {
    		echo "&nbsp;&nbsp;Errror: ".$e->getMessage()."<br/>";
    	}
    	
    	$time2 = microtime();
    	Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }

    private function migratePayoutOptions() {
    	echo "&nbsp;&nbsp;Migrating payout options.....";
    	
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        $selectBuilder->from->add('wd_pa_payoutoptions');

        $result = $selectBuilder->getAllRows();

        $count = 0;
        foreach($result as $record) {
        	$obj = new Gpf_Db_FieldGroup();
        	$obj->setID($record->get('payoptid'));
           	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
        	$obj->setName($record->get('name'));
           	$obj->setType(Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);
           	$obj->setStatus(($record->get('disabled') == 1 ? Gpf_Db_FieldGroup::DISABLED : Gpf_Db_FieldGroup::ENABLED));
        	$obj->save();
        	$count++;
        }
    	echo " ($count) ..... DONE<br/>";
    }

    private function migratePayoutOptionFields() {
    	echo "&nbsp;&nbsp;Migrating payout option fields.....";
    	
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        $selectBuilder->from->add('wd_pa_payoutfields');

        $result = $selectBuilder->getAllRows();

        $count = 0;
        foreach($result as $record) {
        	$obj = new Gpf_Db_FormField();
        	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
        	$obj->setFormId('payout_option_'.$record->get('payoptid'));
        	$obj->setCode($record->get('code'));
        	$obj->setName($record->get('name'));
        	$obj->setType(($record->get('rtype') == 1 ? 'T' : 'L'));
        	$obj->setStatus(($record->get('mandatory') == 1 ? 'M' : 'O'));
        	$obj->save();
        	$count++;
        }
    	echo " ($count) ..... DONE<br/>";
    }
    
    private function migratePayoutHistory() {
    	echo "&nbsp;&nbsp;Migrating payout history.....";
    	
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        $selectBuilder->from->add('wd_pa_accounting');

        $count = 0;
        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	$obj = new Pap_Db_PayoutHistory();
        	$obj->set('payouthistoryid', $record->get('accountingid'));
        	$obj->set('dateinserted', $record->get('dateinserted'));
        	$obj->set('merchantnote', $record->get('note'));
        	$obj->set('datefrom', $record->get('datefrom'));
        	$obj->set('dateto', $record->get('dateto'));
        	if($record->get('wirefile') != '') {
        		$obj->set('exportfile', $record->get('wirefile'));
        	}
        	$obj->save();
        	
        	$this->payoutHistoryIds[] = $record->get('accountingid');
        	$count++;
        }
        
    	echo " ($count) ..... DONE<br/>";
    }    

    private function migratePayouts() {
    	echo "&nbsp;&nbsp;Migrating payous.....";
    	
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('sum(commission)', 'commission');
        $selectBuilder->select->add('affiliateid');
        $selectBuilder->select->add('accountingid');
        
        $selectBuilder->from->add('wd_pa_transactions');
        
        $selectBuilder->where->add('rstatus', '=', Pap3Compatibility_Migration_Pap3Constants::STATUS_APPROVED);
        $selectBuilder->where->add('payoutstatus', '=', Pap3Compatibility_Migration_Pap3Constants::STATUS_APPROVED);
        $selectBuilder->where->add('accountingid', '!=', null);
        
        $selectBuilder->groupBy->add('accountingid');
        $selectBuilder->groupBy->add('affiliateid');

        $count = 0;
        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	$obj = new Pap_Db_Payout();
        	$obj->setUserId($record->get('affiliateid'));
        	$obj->set('payouthistoryid', $record->get('accountingid'));
        	$obj->set('amount', $record->get('commission'));
        	$obj->save();
        	
        	$count++;
        }
    	echo " ($count) ..... DONE<br/>";
    }
}
?>
