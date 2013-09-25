<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: UserInCommissionGroup.class.php 30519 2010-12-20 09:50:12Z iivanco $
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
class Pap_Db_UserInCommissionGroup extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_UserInCommissionGroup::getInstance());
        parent::init();
    }

    public function setUserId($userId) {
        $this->set(Pap_Db_Table_UserInCommissionGroup::USER_ID, $userId);
    }

    public function setCommissionGroupId($commissionGroupId) {
        $this->set(Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, $commissionGroupId);
    }

    public function setStatus($status) {
        $this->set(Pap_Db_Table_UserInCommissionGroup::STATUS, $status);
    }

    public function setDateAdded($dateAdded) {
        $this->set(Pap_Db_Table_UserInCommissionGroup::DATE_ADDED, $dateAdded);
    }

    public function getUserId() {
        return $this->get(Pap_Db_Table_UserInCommissionGroup::USER_ID);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_UserInCommissionGroup::STATUS);
    }

    public function isUserFixed() {
        return Pap_Features_PerformanceRewards_Condition::STATUS_FIXED == $this->get(Pap_Db_Table_UserInCommissionGroup::STATUS);
    }

    public function getCommissionGroupId() {
        return $this->get(Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
    }

    public function setId($userCommissionGroupId) {
        $this->set(Pap_Db_Table_UserInCommissionGroup::ID, $userCommissionGroupId);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_UserInCommissionGroup::ID);
    }

    public function removeUserFromCampaignGroups($campaignId) {
        Pap_Db_Table_UserInCommissionGroup::removeUserFromCampaignGroups($this->getUserId(), $campaignId);
    }

    /**
     * @service user_comm_group add
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function addUsers(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to invite %s %s', '%s', $this->getRowName()));
        $action->setInfoMessage($this->_('%s %s successfully invited', '%s', $this->getRowName()));

        $campaign = new Pap_Common_Campaign();
        $campaign->setId($action->getParam('campaignId'));
        try {
            $campaign->load();
            if ($campaign->getCampaignType() == Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC) {
                $action->setErrorMessage($this->_('Campaign is not private or public with manual approval'));
                $action->addError();
                return $action;
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
            $action->setErrorMessage($this->_('Campaign not exist'));
            $action->addError();
            return $action;
        }
        $commissionGroup = $campaign->getDefaultCommissionGroup();

        foreach ($action->getIds() as $id) {
            $this->addUserNoRpc($action, $action->getParam('campaignId'), $id, $commissionGroup, 'A', $action->getParam('sendNotification') == Gpf::YES);
        }
        return $action;
    }

    /**
     * @service user_comm_group add
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function addUser(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_('%s successfully add', $this->getRowName()));
        $campaignId = $action->getParam('campaignId');

        $cTable = Pap_Db_Table_Commissions::getInstance();
        $commissionGroup = $cTable->getDefaultCommissionGroup($campaignId);

        $this->addUserNoRpc($action, $campaignId, Gpf_Session::getAuthUser()->getPapUserId(), $commissionGroup, 'P', $action->getParam('sendNotification') == Gpf::YES);

        return $action;
    }

    public function addUserToGroupAndSendNotification($campaignId, $userId, $commissionGroupId, $status) {
        $userInCommGroup = $this->createUserInCommGroup($userId, $commissionGroupId, $status);
        try {
            $userInCommGroup->loadFromData(array(Pap_Db_Table_UserInCommissionGroup::USER_ID, Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->addUserToGroup($campaignId, $userId, $commissionGroupId, $status);
            $this->sendInviteMail(Pap_Common_Campaign::getCampaignById($campaignId), $userId);
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
        }
    }

    public function addUserToGroup($campaignId, $userId, $commissionGroupId, $status) {
        $userInCommGroup = $this->createUserInCommGroup($userId, $commissionGroupId, $status);
        $userInCommGroup->setDateAdded(Gpf_Common_DateUtils::getDateTime(time()));

        $userInCommGroup->removeUserFromCampaignGroups($campaignId);

        $userInCommGroup->insert();
    }
    
    private function addUserNoRpc(Gpf_Rpc_Action $action, $campaignId, $userId, $commissionGroupId, $status, $sendNotification = false) {
        try {
            if ($sendNotification) {
                $this->addUserToGroupAndSendNotification($campaignId, $userId, $commissionGroupId, $status);
            } else {
                $this->addUserToGroup($campaignId, $userId, $commissionGroupId, $status);
            }
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UserInCommissionGroup.addUser', Pap_Common_Campaign::getCampaignById($campaignId));
            $action->addOk();
        } catch (Exception $e) {
            $action->setErrorMessage($this->_('User is also in this or other commission group for this campaign'));
            $action->addError();
        }
    }

    private function getRowName() {
        return $this->_('user(s) in commission group');
    }

    public function sendInviteMail(Pap_Common_Campaign $campaign, $userID) {
        if(Gpf::NO == Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_CAMPAIGN_INVITATION)){
            return;
        }

        $affiliate = new Pap_Affiliates_User();
        $affiliate->setId($userID);
        try {
            $affiliate->load();

            $mail = new Pap_Mail_InviteToCampaign();
            $mail->setCampaign($campaign);
            $mail->setUser($affiliate);
            $mail->addRecipient($affiliate->getEmail());
            $mail->send();
        } catch (Gpf_Exception $e) {
        }
    }

    /**
     * @param $userId
     * @param $commissionGroupId
     * @param $status
     * @return Pap_Db_UserInCommissionGroup
     */
    private function createUserInCommGroup($userId, $commissionGroupId, $status) {
        $userInCommGroup = new Pap_Db_UserInCommissionGroup();
        $userInCommGroup->setUserId($userId);
        $userInCommGroup->setCommissionGroupId($commissionGroupId);
        $userInCommGroup->setStatus($status);
        return $userInCommGroup;
    }
}

?>
