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

class Pap3Compatibility_Migration_TaskSpecialCommissions extends Pap3Compatibility_Migration_LongTask {

	private $defaultCampaignId = '';
	
	public function getCount() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('count(transid)', 'count');
        		
        $result = $selectBuilder->getAllRows();
        foreach($result as $record) {
        	return $record->get('count');
        }
        
        return 0;
	}

	public function getSelect() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
     
        $selectBuilder->from->add('wd_pa_transactions', 't');
        $selectBuilder->from->addLeftJoin('wd_pa_campaigncategories', 'cc', 't.campcategoryid=cc.campcategoryid');
        $selectBuilder->from->addInnerJoin('wd_g_users', 'u', 't.affiliateid=u.userid AND u.rtype=4 AND u.deleted=0');
            
        $selectBuilder->where->add('t.transtype', 'in', array(Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_RECURRING,
        													  Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_SIGNUP,
        													  Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_REFERRAL,
        													  Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_REFUND,
        													  Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CHARGEBACK));

        return $selectBuilder;
	}
	
	public function getFullSelect() {
    	$selectBuilder = $this->getSelect();
		$selectBuilder->select->add('t.*');
        $selectBuilder->select->add('cc.campaignid', 'campaignid');
        
        return $selectBuilder;
	}
	
    public function processRecord($record) {
        try {
        	$this->insertTransaction($record);
        } catch(Gpf_Exception $e) {
        	Pap3Compatibility_Migration_OutputWriter::log("<br/>Exception when inserting record ".$e->getMessage());
        }
    }

    private function getDefaultCampaignId() {
    	if($this->defaultCampaignId == '') {
    		$this->defaultCampaignId = Pap_Db_Table_Campaigns::getDefaultCampaignId();
    	}
    	
    	return $this->defaultCampaignId;
    }
    
    protected function insertTransaction($record) {
    	$obj = new Pap_Db_Transaction();
    	
    	$obj->setId($record->get('transid'));
    	$obj->setUserId($record->get('affiliateid'));
    	if($record->get('campaignid') != '') {
    		$obj->setCampaignId($record->get('campaignid'));
    	} else {
    		$obj->setCampaignId($this->getDefaultCampaignId());
    	}
		$obj->setDateInserted($record->get('dateinserted'));
		if($record->get('dateapproved') != '') {
			$obj->setDateApproved($record->get('dateapproved'));
		}
		$obj->setSystemNote("Migrated from PAP3");
		$obj->set('countrycode', $record->get('countrycode'));
		$obj->set('ip', $record->get('ip'));
		$obj->set('refererurl', $record->get('refererurl'));
		$obj->set('browser', $record->get('browser'));
		$obj->setCommission($record->get('commission'));
		$obj->set('data1', $record->get('data1'));
		$obj->set('data2', $record->get('data2'));
		$obj->set('data3', $record->get('data3'));
		$obj->setFixedCost(0);
		$obj->setTotalCost($record->get('totalcost'));
		$obj->setOrderId($record->get('orderid'));
		$obj->setProductId($record->get('productid'));
		$obj->setAllowFirstClickData(GPF::YES);
        $obj->setAllowLastClickData(GPF::YES);
		if($record->get('payoutstatus') == 2) {
			$obj->setPayoutStatus('P');
		} else {
			$obj->setPayoutStatus('U');
		}
		$obj->setClickCount(1);
		$obj->setStatus(Pap3Compatibility_Migration_Pap3Constants::translateStatus($record->get('rstatus')));
		$obj->setType(Pap3Compatibility_Migration_Pap3Constants::translateTransType($record->get('transtype')));

		$transKind = $record->get('transkind');
		if($transKind > Pap3Compatibility_Migration_Pap3Constants::TRANSKIND_SECONDTIER) {
			$tier = $transKind - Pap3Compatibility_Migration_Pap3Constants::TRANSKIND_SECONDTIER;
		} else {
			$tier = 1;
		}
		$obj->setTier($tier);
		$obj->set('bannerid', $record->get('bannerid'));
		$obj->set('commtypeid', $record->get('campcategoryid'));
		if($record->get('accountingid') != '') {
			$obj->set('payouthistoryid', $record->get('accountingid'));
		}
    	$obj->save();
    	
//bannerid               char(8)              utf8_general_ci  YES     MUL     (NULL)           select,insert,update,references
//parentbannerid         varchar(8)           utf8_general_ci  YES     MUL     (NULL)           select,insert,update,references
//parenttransid          char(8)              utf8_general_ci  YES     MUL     (NULL)           select,insert,update,references
//recurringcommid        char(8)              utf8_general_ci  YES             (NULL)           select,insert,update,references
//firstclicktime         datetime             (NULL)           YES             (NULL)           select,insert,update,references
//firstclickreferer      varchar(250)         utf8_general_ci  YES             (NULL)           select,insert,update,references
//firstclickip           varchar(15)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//firstclickdata1        varchar(40)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//firstclickdata2        varchar(40)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//lastclicktime          datetime             (NULL)           YES             (NULL)           select,insert,update,references
//lastclickreferer       varchar(250)         utf8_general_ci  YES             (NULL)           select,insert,update,references
//lastclickip            varchar(15)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//lastclickdata1         varchar(40)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//lastclickdata2         varchar(40)          utf8_general_ci  YES             (NULL)           select,insert,update,references
//trackmethod            char(1)              utf8_general_ci  YES             U                select,insert,update,references
//commtypeid             char(8)              utf8_general_ci  YES     MUL     (NULL)           select,insert,update,references
//payouthistoryid        char(8)              utf8_general_ci  YES     MUL     (NULL)           select,insert,update,references
    	
    	$this->countSales++;
    }
}
?>
