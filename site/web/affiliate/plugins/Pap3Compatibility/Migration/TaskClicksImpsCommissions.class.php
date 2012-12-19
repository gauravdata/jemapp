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

class Pap3Compatibility_Migration_TaskClicksImpsCommissions extends Pap3Compatibility_Migration_LongTask {
	
	public function getCount() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('t.rstatus', 'rstatus');
        
    	$count = 0;
        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	$count++;
        }
        		
        return $count;
	}

	public function getSelect() {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		     
        $selectBuilder->from->add('wd_pa_transactions', 't');

        $selectBuilder->from->addLeftJoin('wd_pa_campaigncategories', 'cc', 't.campcategoryid=cc.campcategoryid');
        $selectBuilder->from->addInnerJoin('wd_g_users', 'u', 't.affiliateid=u.userid AND u.rtype=4 AND u.deleted=0');

        $selectBuilder->where->add('t.transtype', '=', $this->param);
        $selectBuilder->where->add('t.commission', '>', 0);
        
        $selectBuilder->groupBy->add("DATE_FORMAT(t.dateinserted, '%Y-%m-01 00:00:00')");
    	$selectBuilder->groupBy->add("t.rstatus");
    	$selectBuilder->groupBy->add("t.transtype");
    	$selectBuilder->groupBy->add("t.affiliateid");
    	$selectBuilder->groupBy->add("t.campcategoryid");
    	$selectBuilder->groupBy->add("t.accountingid");
    	$selectBuilder->groupBy->add("t.transkind");
    	$selectBuilder->groupBy->add("t.payoutstatus");
    	
        return $selectBuilder;
	}
	
	public function getFullSelect() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('cc.campaignid', 'campaignid');
		$selectBuilder->select->add('t.affiliateid', 'affiliateid');
        $selectBuilder->select->add("DATE_FORMAT(t.dateinserted, '%Y-%m-01 00:00:00')", 'dateinserted');
        $selectBuilder->select->add('t.rstatus', 'rstatus');
        $selectBuilder->select->add('t.transtype', 'transtype');
        $selectBuilder->select->add('t.campcategoryid', 'campcategoryid');
        $selectBuilder->select->add('t.accountingid', 'accountingid');
        $selectBuilder->select->add('t.transkind', 'transkind');
        $selectBuilder->select->add('t.payoutstatus', 'payoutstatus');
        $selectBuilder->select->add('sum(t.count)', 'count');
        $selectBuilder->select->add('sum(t.commission)', 'commission');
        return $selectBuilder;
	}
	
    public function processRecord($record) {
        try {
        	$this->insertSummarizedTransaction($record);
        } catch(Gpf_Exception $e) {
        	Pap3Compatibility_Migration_OutputWriter::log("<br/>Exception when inserting record ".$e->getMessage());
        }
    }

    protected function insertSummarizedTransaction($record) {
    	$obj = new Pap_Common_Transaction();
    	
    	$obj->setUserId($record->get('affiliateid'));
    	$obj->setCampaignId($record->get('campaignid'));
		$obj->setDateInserted($record->get('dateinserted'));
		$obj->setType(Pap3Compatibility_Migration_Pap3Constants::translateTransType($record->get('transtype')));
		$obj->setAllowFirstClickData(GPF::YES);
        $obj->setAllowLastClickData(GPF::YES);
		$obj->setStatus(Pap3Compatibility_Migration_Pap3Constants::translateStatus($record->get('rstatus')));
		
		if($record->get('rstatus') == Pap3Compatibility_Migration_Pap3Constants::STATUS_APPROVED) {
			$obj->setDateApproved($record->get('dateinserted'));
		}
		
		$obj->setCommission($record->get('commission'));
		$obj->setSystemNote("Migrated from PAP3");
		if($record->get('payoutstatus') == 2) {
			$obj->setPayoutStatus('P');
		} else {
			$obj->setPayoutStatus('U');
		}
		$obj->setClickCount($record->get('count'));

		$transKind = $record->get('transkind');
		if($transKind > Pap3Compatibility_Migration_Pap3Constants::TRANSKIND_SECONDTIER) {
			$tier = $transKind - Pap3Compatibility_Migration_Pap3Constants::TRANSKIND_SECONDTIER;
		} else {
			$tier = 1;
		}
		$obj->setTier($tier);
		$obj->set('commtypeid', $record->get('campcategoryid'));
		
    	$obj->save();
    }
}
?>
