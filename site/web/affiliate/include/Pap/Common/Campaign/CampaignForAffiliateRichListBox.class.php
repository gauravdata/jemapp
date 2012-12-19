<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * returns all active and not stopped campaigns
 * @package PostAffiliatePro
 */
class Pap_Common_Campaign_CampaignForAffiliateRichListBox extends Pap_Common_Campaign_CampaignRichListBox {
	
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getSearchRecordSet() {
        return $this->searchFromRecordSet($this->createRecordSet());
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getChachedRecordSet() {
        return $this->cachedFromRecordSet($this->createRecordSet());
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getIdRecordSet() {
        return $this->searchIdFromRecordSet($this->createRecordSet());
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    private function createRecordSet() {
    	$affiliateCampaigns = new Pap_Common_Campaign_CampaignForAffiliateRichListBox_AffiliateCampaigns($this->getCampaignRecordSetForAffiliate(),
    	Gpf_Session::getAuthUser()->getPapUserId(), Pap_Common_Campaign_CampaignForAffiliateRichListBox::ID);
    	return $affiliateCampaigns->getAffiliateCampaigns();
    }
    
    private function getCampaignRecordSetForAffiliate() {
        $selectBuilder = $this->createSelectBuilder();
        $selectBuilder->select->add('c.'.Pap_Db_Table_Campaigns::STATUS, 'rstatus');
        $selectBuilder->select->add('c.'.Pap_Db_Table_Campaigns::TYPE, 'rtype');
        Gpf_Plugins_Engine::extensionPoint('Pap_Common_Campaign_CampaignForAffiliateRichListBox.getCampaignRecordSetForAffiliate', 
        new Pap_Affiliates_Promo_SelectBuilderCompoundFilter($selectBuilder));

        $result = new Gpf_Data_RecordSet();
        $result->load($selectBuilder);
        
        return $result;
    }
}

class Pap_Common_Campaign_CampaignForAffiliateRichListBox_AffiliateCampaigns extends Pap_Common_Campaign_AffiliateCampaigns {
	
	protected function initAffiliateCampaigns() {
		$this->affiliateCampaigns = new Gpf_Data_RecordSet();
        $this->affiliateCampaigns->setHeader(array(Pap_Common_Campaign_CampaignForAffiliateRichListBox::ID, Pap_Common_Campaign_CampaignForAffiliateRichListBox::VALUE));
        $this->affiliateCampaigns->add(array('', $this->_('All')));
	}
	
	protected function insertAffiliateCampaign(Gpf_Data_Record $campaign) {
		$this->affiliateCampaigns->add(array($record->get(Pap_Common_Campaign_CampaignForAffiliateRichListBox::ID), $record->get(Pap_Common_Campaign_CampaignForAffiliateRichListBox::VALUE)));
	}
}
?>
