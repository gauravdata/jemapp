<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_OnAffiliateJoinToCampaign extends Pap_Mail_CampaignMailBase {
	
	/**
	 * @var Pap_Common_Campaign
	 */

    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'on_affiliate_join_to_campaign.stpl';
        $this->templateName = Gpf_Lang::_runtime('Merchant - New affiliate joined campaign');
        $this->subject = Gpf_Lang::_runtime('New affiliate wants to join campaign');
    }
    protected function initTemplateVariables() {
        $this->addVariable('affiliate_join_campaign_approve_link', $this->_("Affiliate Join Campaign Approve Link"));
        $this->addVariable('affiliate_join_campaign_decline_link', $this->_("Affiliate Join Campaign Decline Link"));
        parent::initTemplateVariables();
    }

    protected function setVariableValues() {
        $quickTaskGroup = new Gpf_Tasks_QuickTaskGroup();
        $this->setVariable('affiliate_join_campaign_approve_link', 
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_APPROVED));
        $this->setVariable('affiliate_join_campaign_decline_link', 
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_DECLINED));
        parent::setVariableValues();
    }   
    
    private function createApproveDeclineTask(Gpf_Tasks_QuickTaskGroup $quickTaskGroup, $newStatus) {               
        $actionRequest = new Gpf_Rpc_ActionRequest('Pap_Features_Common_AffiliateGroupForm', 'changeStatus');
       
        $commissionGroup = Pap_Db_Table_CommissionGroups::getInstance();

        $userInCommissionGroupId = $commissionGroup->getUserInCommissionGroup($this->campaign->getId(), $this->user->getId());

        $actionRequest->addParam('status', $newStatus);
        $actionRequest->addParam('ids', new Gpf_Rpc_Array(array($userInCommissionGroupId)));   
        
        $quickTask = new Gpf_Tasks_QuickTask($actionRequest);
        $quickTaskGroup->add($quickTask);
        return $quickTask->getUrl();
    }
}
