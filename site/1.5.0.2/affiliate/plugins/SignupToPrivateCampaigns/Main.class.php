<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class SignupToPrivateCampaigns_Main extends SignupToCampaign_Main {

    private static $instance;

    /**
     * @return SignupToPrivateCampaigns_Main
     */
    public static function getHandlerInstance() {
        if (self::$instance == null) {
            self::$instance = new SignupToPrivateCampaigns_Main();
        }
        return self::$instance;
    }

    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(SignupToPrivateCampaigns_Config::CAMPAIGNS_IDS, '');
    }

    public function firstTimeApproved(Pap_Affiliates_User $user){
        $userInCommision = new Pap_Db_UserInCommissionGroup();

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Campaigns::getInstance(), 'ca');
        
        $selectBuilder->from->add('qu_pap_commissiongroups', 'c');
        $selectBuilder->from->addInnerJoin('qu_pap_userincommissiongroup', 'u', 'u.commissiongroupid=c.commissiongroupid');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'ca', 'ca.'.Pap_Db_Table_Campaigns::ID.'=c.campaignid');
        $selectBuilder->where->add('u.userid','=',$user->getId());
        foreach ($selectBuilder->getAllRowsIterator() as $row){
            $campaign = new Pap_Common_Campaign();
            $campaign->fillFromRecord($row);
            $userInCommision->sendInviteMail($campaign,$user->getId());
        }
    }

    public function afterSignup(Pap_Contexts_Signup $signupContext) {
        $campaignsIds = $this->getCampaignsIdsFromString(Gpf_Settings::get(SignupToPrivateCampaigns_Config::CAMPAIGNS_IDS));
        foreach ($campaignsIds as $campaignId) {
            $this->signUserToCampaign($signupContext->getUserObject()->getId(), $campaignId, $signupContext->getFormObject());
        }
    }

    /**
     * @return array
     */
    public function getCampaignsIdsFromString($campaignsIds) {
        $campaignsIdsArray = explode(',', $campaignsIds);
        foreach ($campaignsIdsArray as $key => $campaignId) {
            $campaignsIdsArray[$key] = trim($campaignId);
            if (strlen($campaignsIdsArray[$key]) == 0) {
                unset($campaignsIdsArray[$key]);
                continue;
            }
            if (strlen($campaignsIdsArray[$key]) != 8) {
                return false;
            }
        }
        return $campaignsIdsArray;
    }
}

?>
