<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
class SignupToCampaign_Main extends Gpf_Plugins_Handler {

    private static $instance;

    /**
     * @return SignupToCampaign_Main
     */
    public static function getHandlerInstance() {
        if (self::$instance == null) {
            self::$instance = new SignupToCampaign_Main();
        }
        return self::$instance;
    }

    public function afterSignup(Pap_Contexts_Signup $signupContext) {
        $form = $signupContext->getFormObject();
        if (!$form->existsField('a_cid')) {
            return false;
        }
        $this->signUserToCampaign($signupContext->getUserObject()->getId(), $form->getFieldValue('a_cid'), $form);
    }

    public function afterSignupFailed(Pap_Contexts_Signup $signupContext) {
        $form = $signupContext->getFormObject();
        if ($form->getFieldError('username') == '') {
            return;
        }
        try {
            $userId = $this->getUserId($form->getFieldValue('username'));
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('SignupToCampaignPlugin: user with username '.$form->getFieldValue('username').' does not exist');
            return;
        }
        if ($this->signUserToCampaign($userId, $form)) {
            $form->setInfoMessage($this->_('You have been successfuly signed up'));
            $form->setErrorMessage('');
            $form->setFieldError('username', '');
            $form->setSuccessful();
        }
    }

    protected function getUserId($username) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('pu.'.Pap_Db_Table_Users::ID, 'id');
        $select->from->add(Pap_Db_Table_Users::getName(), 'pu');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu',
            'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=gu.'.Gpf_Db_Table_Users::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
            'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
        $select->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $select->where->add('pu.'.Pap_Db_Table_Users::DELETED, '<>', Gpf::YES);
        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, '=', $username);
        return $select->getOneRow()->get('id');
    }

    protected function signUserToCampaign($userId, $campaignId, Gpf_Rpc_Form $form) {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('SignupToCampaignPlugin: Invalid campaign '.$campaignId);
            return false;
        }
        if ($campaign->getCampaignType() == Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC) {
            return false;
        }
        $userInCommGroup = new Pap_Db_UserInCommissionGroup();
        
        $user = new pap_common_user();
        $user->setId($userId);
        $user->load();
        if($user->getStatus() == Pap_Common_Constants::STATUS_PENDING){
            $userInCommGroup->addUserToGroup($campaign->getId(), $userId,
            $campaign->getDefaultCommissionGroup(), Pap_Common_Constants::STATUS_APPROVED);
            return true;
        }
        $userInCommGroup->addUserToGroupAndSendNotification($campaign->getId(), $userId,
        $campaign->getDefaultCommissionGroup(), Pap_Common_Constants::STATUS_APPROVED);
        return true;
    }
}

?>
