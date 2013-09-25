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

class Pap3Compatibility_Migration_TaskClickCounts extends Pap3Compatibility_Migration_TaskClickImpCounts {
	
	public function getCount() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('t.affiliateid', 'affiliateid');

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

        $selectBuilder->where->add('t.transtype', '=', Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CLICK);

        return $selectBuilder;
	}
	
	public function getFullSelect() {
    	$selectBuilder = $this->getSelect();
        
        $selectBuilder->select->add('t.count', 'rawcount');
        $selectBuilder->select->add('t.count', 'uniquecount');
        $selectBuilder->select->add('t.affiliateid', 'affiliateid');
		$selectBuilder->select->add('cc.campaignid', 'campaignid');
        $selectBuilder->select->add('t.bannerid', 'bannerid');
        $selectBuilder->select->add("DATE_FORMAT(t.dateinserted, '%Y-%m-%d %H:00:00')", 'date');
    	        
        return $selectBuilder;
	}
	
    public function beforeRecordsProcessed() {
		$this->cache = array();
		$this->cacheCount = 0;
    }
    	
    public function processRecord($record) {
    	$this->putToCache($record);

        if($this->cacheCount > 50) {
        	$this->saveFromCache(Pap_Common_Constants::TYPE_CLICK);
        }
    }
    
    public function afterRecordsProcessed() {
    	if($this->cacheCount > 0) {
			$this->saveFromCache(Pap_Common_Constants::TYPE_CLICK);
		}
    }
}
?>
