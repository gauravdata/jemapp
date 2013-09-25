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

class Pap3Compatibility_Migration_TaskImpCounts extends Pap3Compatibility_Migration_TaskClickImpCounts {
	
	public function getCount() {
    	$selectBuilder = $this->getSelect();
        $selectBuilder->select->add('i.affiliateid', 'affiliateid');

	    $selectBuilderCount = new Gpf_SqlBuilder_SelectBuilder();
	    $selectBuilderCount->select->add('count(*)', 'count');
	    $selectBuilderCount->from->addSubselect($selectBuilder, 'sub');
	    $row = $selectBuilderCount->getOneRow();
        return $row->get('count');
	}

	public function getSelect() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        
        $selectBuilder->from->add('wd_pa_impressions', 'i');
        
        $selectBuilder->from->addLeftJoin('wd_pa_banners', 'b', 'i.bannerid=b.bannerid');
        
        $selectBuilder->groupBy->add("DATE_FORMAT(i.dateimpression, '%Y-%m-%d %H:00:00')");
    	$selectBuilder->groupBy->add("i.affiliateid");
    	$selectBuilder->groupBy->add("i.bannerid");

        $selectBuilder->orderBy->add("DATE_FORMAT(i.dateimpression, '%Y-%m-%d %H:00:00')");
        $selectBuilder->orderBy->add("i.affiliateid");
            	
        return $selectBuilder;
	}
	
	public function getFullSelect() {
    	$selectBuilder = $this->getSelect();
        
        $selectBuilder->select->add('sum(i.all_imps_count)', 'rawcount');
        $selectBuilder->select->add('sum(i.unique_imps_count)', 'uniquecount');
        $selectBuilder->select->add('i.affiliateid', 'affiliateid');
        $selectBuilder->select->add('b.campaignid', 'campaignid');
        $selectBuilder->select->add('i.bannerid', 'bannerid');
        $selectBuilder->select->add("DATE_FORMAT(i.dateimpression, '%Y-%m-%d %H:00:00')", 'date');
    	    	        
        return $selectBuilder;
	}
	
    public function beforeRecordsProcessed() {
		$this->cache = array();
		$this->cacheCount = 0;
    }
    	
    public function processRecord($record) {
    	$this->putToCache($record);

        if($this->cacheCount > 500) {
        	$this->saveFromCache(Pap_Common_Constants::TYPE_CPM);
        }
    }
    
    public function afterRecordsProcessed() {
    	if($this->cacheCount > 0) {
			$this->saveFromCache(Pap_Common_Constants::TYPE_CPM);
		}
    }
}
?>
