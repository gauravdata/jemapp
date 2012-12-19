<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Maros Fric
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
class Pap_Affiliates_Promo_Campaigns extends Gpf_Object {
    
	/**
	 * returns campaigns
	 *
	 * @service campaign read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$result = $this->loadCampaigns();
		$result = $this->addCommissions($result);
		
		return $result;
	}
	
	private function loadCampaigns() {
        $result = new Gpf_Data_RecordSet('id');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::ID, 'id');
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::NAME, 'name');
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::STATUS, 'status');
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::DESCRIPTION, 'description');
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::LOGO_URL, 'logourl');
        
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName());
        
        $selectBuilder->where->add(Pap_Db_Table_Campaigns::STATUS, 'IN', Pap_Affiliates_Promo_Campaigns::getCampaignAllowedStatuses());

        $selectBuilder->orderBy->add(Pap_Db_Table_Campaigns::ORDER);
        
        $result->load($selectBuilder);
        return $result;
	}
	
	private function addCommissions(Gpf_Data_RecordSet $rs ) {
	    $cTable = Pap_Db_Table_Commissions::getInstance();
        $rsCommissions = $cTable->getAllCommissionsInCampaign('', '');
        $rs->addColumn('commissions', 'N');
        
        $newRs = new Gpf_Data_RecordSet();
        $newRs->setHeader($rs->getHeader());
        
        foreach ($rs as $record) {
        	if($cTable->findCampaignInCommExistsRecords($record->get("id"), $rsCommissions)) {
        		$record->set('commissions', $this->getCommissionsDescription($record->get("id"), $rsCommissions));
        		$newRs->addRecord($record);
        	}
        }

        return $newRs;		
	}

    /**
     * returns text description about campaign commissions
     *
     * @param string $campaignId
     * @param Gpf_Data_RecordSet $rsCommissions
     * @return string
     */
    private function getCommissionsDescription($campaignId, Gpf_Data_RecordSet $rsCommissions) {
    	if($rsCommissions->getSize() == 0) {
    		return $this->_('none active');
    	}
    	
    	$commissions = array();
    	$maxTiers = 1;
    	foreach($rsCommissions as $record) {
    		if($campaignId != $record->get("campaignid")) {
    			continue;
    		}
    		
    		$rType = $record->get('rtype');
    		$tier = $record->get('tier');
    		if($tier > $maxTiers) {
    			$maxTiers = $tier;
    		}
    		$commissions[$rType][$tier]['commtype'] = $record->get(Pap_Db_Table_Commissions::TYPE);
    		$commissions[$rType][$tier]['value'] = $record->get(Pap_Db_Table_Commissions::VALUE);
    	}

    	$description = '';

    	for($i=1; $i<=$maxTiers; $i++) {
    		if(isset($commissions[Pap_Common_Constants::TYPE_CPM][$i])) {
    			$description .= ($description != '' ? ', ' : '');
    			$description .= $this->_('CPM').': '.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commissions[Pap_Common_Constants::TYPE_CPM][$i]['value']);
    		}
    		if(isset($commissions[Pap_Common_Constants::TYPE_CLICK][$i])) {
    			$description .= ($description != '' ? ', ' : '');
    			$description .= $this->_('per click').': '.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commissions[Pap_Common_Constants::TYPE_CLICK][$i]['value']);
    		}
    		if(isset($commissions[Pap_Common_Constants::TYPE_SALE][$i])) {
    			$description .= ($description != '' ? ', ' : '');

    			$description .= $this->_('per sale').': '.
    			Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commissions[Pap_Common_Constants::TYPE_SALE][$i]['value'],
    			$commissions[Pap_Common_Constants::TYPE_SALE][$i]['commtype']);
    		}

    	}
    	if($description == '') {
    		$description = $this->_('none active');
    	}
    	
    	return $description;
    }
    
    /**
     * returns array of campaign statuses that can be displayed in affiliate panel
     *
     * @return array
     */
    public static function getCampaignAllowedStatuses() {
    	return array(Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE, Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED);
    }
}
?>
