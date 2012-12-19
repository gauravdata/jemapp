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

class Pap3Compatibility_Migration_TaskClickImpCounts extends Pap3Compatibility_Migration_LongTask {

    protected $cache = array();
    protected $cacheCount = 0;

    protected function putToCache($record) {
        $bannerId = $record->get('bannerid');
        $affiliateId = $record->get('affiliateid');
        $campaignId = $record->get('campaignid');
        $key = $bannerId.'#'.$affiliateId.'#'.$campaignId;
         
        $date = $record->get('date');

        // increase number of cached records
        $increment = 0;
        if(!isset($this->cache[$key])) {
            $increment = 1;
        }
        if(!isset($this->cache[$key][$date])) {
            $increment = 1;
        }
        $this->cacheCount += $increment;

        @$this->cache[$key][$date]['raw'] += $record->get('rawcount');
        @$this->cache[$key][$date]['uni'] += $record->get('uniquecount');
    }

    protected function saveFromCache($dataType) {
        foreach($this->cache as $key => $data1) {
            // for every month
            foreach($data1 as $date => $data2) {
	                $this->insertedCount++;
	
	                $temp = explode('#', $key);
	                $obj = $this->createClickImpObject($dataType);
	                $obj->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
	                $obj->setUserId($temp[1]);
	                $obj->setBannerId($temp[0]);
	                $obj->setCampaignId($temp[2]);
	                $obj->setTime($date);
	                
                    try {
                        $obj->loadFromData(array(Pap_Stats_Table::USERID,
                                                 Pap_Stats_Table::BANNERID,
                                                 Pap_Stats_Table::CAMPAIGNID,
                                                 Pap_Stats_Table::DATEINSERTED));
                    } catch (Gpf_DbEngine_NoRowException $e) {
                        $obj->setPersistent(false);
                    }
	                $obj->addRawCount($data2['raw']);
	                $obj->addUniqueCount($data2['uni']);
	                $obj->save();
            }
        }

        // clear cache
        unset($this->cache);
        $this->cache = array();
        $this->cacheCount = 0;
    }

    /**
     * @return saveAndIncrementClickImpCount
     */
    public function createClickImpObject($dataType) {
        if($dataType == Pap_Common_Constants::TYPE_CLICK) {
            return new Pap_Db_Click();
        } else {
            return new Pap_Db_Impression();
        }
    }
}
?>
