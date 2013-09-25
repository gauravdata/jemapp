<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliatePro plugins
 */
class Pap_Features_PrivateCampaigns_Main extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_PrivateCampaigns_Main();
    }

    public function initViewColumns(Pap_Merchants_Campaign_CampaignsGrid $campaignsGrid) {
        $campaignsGrid->addViewColumn('rtype', $this->_("Type"), true);

        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    public function initDefaultView(Pap_Merchants_Campaign_CampaignsGrid $campaignsGrid) {
        $campaignsGrid->addDefaultViewColumn('rtype', '100', 'N');

        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    public function setAffStatus(Gpf_Data_Record $record) {
        try {
            $record->add('affstatus', Pap_Db_Table_UserInCommissionGroup::getStatus($record->get('id'),
            Gpf_Session::getAuthUser()->getPapUserId()));
        } catch (Gpf_DbEngine_NoRowException $e) {
            switch ($record->get('rtype')) {
                case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL:
                    $record->add('affstatus', '');
                    break;
                case Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION:
                    throw new Gpf_Exception($this->_('Private campaign'));
            }
        }
    }

    public function filterBanners (Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        $selectBuilder->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(),'cg','cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
        $onCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $onCondition->add('cg.'.Pap_Db_Table_CommissionGroups::ID, '=', 'uic.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, 'AND', false);
        $onCondition->add('uic.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
        $selectBuilder->from->addLeftJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'uic', $onCondition->toString());
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('c.'.Pap_Db_Table_Campaigns::TYPE, '=', Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC, 'OR');
        $condition->add('uic.'.Pap_Db_Table_UserInCommissionGroup::STATUS, '=', Pap_Common_Constants::STATUS_APPROVED, 'OR');
        $condition->add('uic.'.Pap_Db_Table_UserInCommissionGroup::STATUS, '=', Pap_Features_PerformanceRewards_Condition::STATUS_FIXED, 'OR');
        $selectBuilder->where->addCondition($condition);
        $selectBuilder->groupBy->add('b.'.Pap_Db_Table_Banners::ID);
    }



    public function getBanners(Gpf_Data_RecordSet $recordSet) {
        $result = $recordSet->toShalowRecordSet();
        foreach ($recordSet as $record) {
            try {
                try {
                    Pap_Db_Table_UserInCommissionGroup::getStatus($record->get('campaignid'), Gpf_Session::getAuthUser()->getPapUserId());
                } catch (Gpf_DbEngine_NoRowException $e) {
                    if ($record->get('ctype') == Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION) {
                        throw new Gpf_Exception($this->_('Private campaign'));
                    }
                }
                $result->addRecord($record);
            } catch (Gpf_Exception $e) {
            }
        }
        $recordSet->clear();
        $recordSet->loadFromObject($result->toObject());
    }

    public function getCampaigns(Gpf_Data_Record $record) {
        try {
            Pap_Db_Table_UserInCommissionGroup::getStatus($record->get('id'), Gpf_Session::getAuthUser()->getPapUserId());
        } catch (Gpf_DbEngine_NoRowException $e) {
            if ($record->get('rtype') == Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION) {
                throw new Gpf_Exception($this->_('Private campaign'));
            }
        }
    }

    public function getCommissionGroup(Pap_Contexts_Tracking $context) {
        if ($context->getCampaignObject()->getCampaignType() != Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
            try {
                $status = Pap_Db_Table_UserInCommissionGroup::getStatus($context->getCampaignObject()->getId(), $context->getUserObject()->getId());
                if ($status != Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED &&
                $status != Pap_Features_PerformanceRewards_Condition::STATUS_FIXED) {
                    throw new Gpf_Exception('');
                }
            } catch (Gpf_Exception $e) {
                $context->debug('    STOPPING, User is not approved in this campaign!');
                $context->setDoCommissionsSave(false);
                $context->setDoTrackerSave(false);
            }
        }
    }

    /**
     * Create private campaigns select builder
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getPrivateCampaignsSqlBuilder() {
        $sqlPrivateCampaigns = new Gpf_SqlBuilder_SelectBuilder();
        $sqlPrivateCampaigns->select->add('cg.campaignid');
        $sqlPrivateCampaigns->from->add(Pap_Db_Table_CommissionGroups::getName(), 'cg');
        $sqlPrivateCampaigns->from->addInnerJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'uicg',
            "cg.commissiongroupid = uicg.commissiongroupid AND uicg.userid='" . Gpf_Session::getInstance()->getAuthUser()->getPapUserId() . "'");
        $sqlPrivateCampaigns->where->add('uicg.' . Pap_Db_Table_UserInCommissionGroup::STATUS, '=', Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED, 'OR');
        $sqlPrivateCampaigns->where->add('uicg.' . Pap_Db_Table_UserInCommissionGroup::STATUS, '=', Pap_Features_PerformanceRewards_Condition::STATUS_FIXED, 'OR');
        return $sqlPrivateCampaigns;
    }

    public function getBannerSelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        //if private campaigns are selected, affiliate should be able to see
        //just banners from public campaigns or campaigns, where he is invited
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            $selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
            "c.campaignid=b.campaignid AND (c.rtype<>'" . Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION . "' OR c.campaignid IN (" .
            $this->getPrivateCampaignsSqlBuilder()->toString() . "))");
        }
    }

    public function getCampaignsSelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        //if private campaigns are selected, affiliate should be able to see
        //just public campaigns or campaigns, where he is invited
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            $cond = new Gpf_SqlBuilder_CompoundWhereCondition();
            $cond->add('c.'.Pap_Db_Table_Campaigns::TYPE, 'NOT IN', array(Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION, Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL), 'OR');
            $cond->add('c.'.Pap_Db_Table_Campaigns::ID, 'IN', $this->getPrivateCampaignsSqlBuilder(), 'OR', false);
            $selectBuilder->where->addCondition($cond);
        }
    }

    public function sendNotificationToAffiliate(Pap_Features_PrivateCampaigns_MailContext $mailContext) {
        $campaign = $mailContext->getCampaign();
        $userInCommGroup = $mailContext->getUserInCommissionGroup();
        if (Gpf::YES == Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN) &&
        $campaign !== null && ($campaign->getCampaignType() == Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL ||
        $campaign->getCampaignType() == Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION) &&
        $userInCommGroup->getStatus() != Pap_Common_Constants::STATUS_PENDING) {
            $mail = $this->createMail($userInCommGroup->getStatus());
            $mail->setCampaign($campaign);
            try {
                $this->sendMailTo($mail, $mailContext->getUser(), $mailContext->getUser()->getEmail());
            } catch (Gpf_DbEngine_NoRowException $e) {
            }

        }
    }

    public function sendNotificationToMerchant(Pap_Common_Campaign $campaign) {
        if (Gpf::YES == Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_JOIN_TO_CAMPAIGN) &&
        $campaign !== null && $campaign->getCampaignType() == Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL) {
            $mail = new Pap_Mail_OnAffiliateJoinToCampaign();
            $mail->setCampaign($campaign);
            try {
                $this->sendMailTo($mail, $this->getUser(), $this->getMerchantNotificationEmail($campaign));
            } catch (Gpf_DbEngine_NoRowException $e) {
            }
        }
    }

    public function createResultSelectAffGrid(Gpf_Common_SelectBuilderCompoundRecord $context) {
        $campaignFilter = $context->getParams()->get('filters')->getFilter('campaignid');
        if (count($campaignFilter) > 0) {
            $this->addCampaignFilterToSelect($context, $campaignFilter[0]->getValue());
        }
    }

    public function processMassMailFilter(Gpf_Common_SelectBuilderCompoundRecord $context) {
        $filter = $context->getParams()->get('filter');
        if ($filter->getCode() == 'campaignid') {
            $this->addCampaignFilterToSelect($context, $filter->getValue());
        }
    }

    private function addCampaignFilterToSelect(Gpf_Common_SelectBuilderCompoundRecord $selectRecord, $campaignId){
        $select = $selectRecord->getSelectBuilder();
        $select->where->add('cg.campaignid','=',$campaignId);
        $select->from->addLeftJoin(Pap_Db_Table_UserInCommissionGroup::getName(),'uic','uic.userid=u.userid');
        $select->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(),'cg','cg.commissiongroupid=uic.commissiongroupid');
        $select->groupBy->add('u.userid');
    }

    private function getMerchantNotificationEmail(Pap_Common_Campaign $campaign) {
        $setting = new Gpf_Db_Setting();
        $setting->setAccountId($campaign->getAccountId());
        $setting->setName(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL);
        try{
            $setting->loadFromData(array(Gpf_Db_Table_Settings::ACCOUNTID, Gpf_Db_Table_Settings::NAME));
            if($setting->getValue() != '') {
                return $setting->getValue();
            }
        } catch (Gpf_Exception $e) {
        }
        return Pap_Common_User::getMerchantEmail();
    }

    /**
     * @param $status
     * @return Pap_Mail_CampaignMailBase
     */
    private function createMail($status) {
        if ($status == Pap_Common_Constants::STATUS_APPROVED ||
        $status == Pap_Features_PerformanceRewards_Condition::STATUS_FIXED) {
            return new Pap_Mail_OnMerchantApproveAffiliateToCampaign();
        }
        return new Pap_Mail_OnMerchantDeclineAffiliateForCampaign();
    }

    private function sendMailTo(Pap_Mail_CampaignMailBase $mail, Pap_Common_User $user, $recipient) {
        $mail->setUser($user);
        $mail->addRecipient($recipient);
        $mail->send();
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     */
    private function getUser() {
        $user = new Pap_Common_User();
        $user->setId(Gpf_Session::getAuthUser()->getPapUserId());
        $user->load();
        return $user;
    }
}
?>
