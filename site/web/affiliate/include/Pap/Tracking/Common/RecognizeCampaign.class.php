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
abstract class Pap_Tracking_Common_RecognizeCampaign extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
    private $campaignsCache = array();
    
    public function __construct() {
    }
    
    public final function recognize(Pap_Contexts_Tracking $context) {
        $context->debug('Recognizing campaign started');

        $campaign = $this->recognizeCampaigns($context);

        if($campaign != null) {
        	$this->onCampaignRecognized($campaign, $context);
            $context->setCampaignObject($campaign);
        } else {
            $context->debug('No campaign recognized!');
            $context->setDoTrackerSave(false);
            $context->setDoCommissionsSave(false);
        }
         
        $context->debug('Recognizing campaign ended');
    }
    
    protected function onCampaignRecognized(Pap_Common_Campaign $campaign, Pap_Contexts_Tracking $context) {
        if ($context->getAccountId() != null && $context->getAccountId() != '' && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
            $context->debug('AccountId already recognized before recognizing campaign.');
            return;
        }

        $context->debug('AccountId recognized from Campaign, set: ' . $campaign->getAccountId());
        $context->setAccountId($campaign->getAccountId(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
    }

    /**
     * @return Pap_Common_Campaign
     */
    protected abstract function recognizeCampaigns(Pap_Contexts_Tracking $context);

    /**
     * gets campaign by campaign id
     * @param $campaignId
     * @return Pap_Common_Campaign
     * @throws Gpf_Exception
     */
    public function getCampaignById(Pap_Contexts_Tracking $context, $campaignId) {
        if($campaignId == '') {
            throw new Gpf_Exception('Can not get campaign. Empty campaign id');
        }
        
        if (isset($this->campaignsCache[$campaignId])) {
            return $this->campaignsCache[$campaignId];
        }
        
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        $campaign->load();
        $this->checkCampaign($context, $campaign);
        $this->campaignsCache[$campaignId] = $campaign;
        return $campaign;
    }
    
    private function checkCampaign(Pap_Contexts_Tracking $context, Pap_Common_Campaign $campaign) {
        if ($this->isAccountRecognizedNotFromDefault($context) && $campaign->getAccountId() != $context->getAccountId()) {
            $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' cannot be used with accountId: '". $context->getAccountId() ."'!");
            throw new Gpf_Exception("Campaign is from differen account");
        }
        $status = $campaign->getCampaignStatus();
        if($status == Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED
            || $status == Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED_INVISIBLE) {
            $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' is stopped, cannot be used!");
            throw new Gpf_Exception("Campaign stopped");
        }
        if($status == Pap_Db_Campaign::CAMPAIGN_STATUS_DELETED) {
            $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' is deleted, cannot be used!");
            throw new Gpf_Exception("Campaign deleted");
        }
    }
    
    private function isAccountRecognizedNotFromDefault(Pap_Contexts_Tracking $context) {
        if ($context->getAccountId() != null && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
            return true;
        } 
        return false;
    }
    
    /**
     * @return Pap_Common_Campaign
     * @throws Gpf_Exception
     */
    public function getDefaultCampaign(Pap_Contexts_Tracking $context) {
        $context->debug('Loading default campaign for account: '.$context->getAccountId());
        $defaultcampaignid = Pap_Db_Table_Campaigns::getDefaultCampaignId($context->getAccountId());
        $context->debug('Loading default campaign by Id: '.$defaultcampaignid);
        return $this->getCampaignById($context, $defaultcampaignid);
    }
    
    /**
     * @param $message
     * @throws Pap_Tracking_Exception
     */
    protected function logAndThrow(Pap_Contexts_Tracking $context, $message) {
        $context->debug($message);
        throw new Pap_Tracking_Exception($message);
    }
}

?>
