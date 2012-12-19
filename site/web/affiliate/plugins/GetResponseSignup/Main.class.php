<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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
class GetResponseSignup_Main extends Gpf_Plugins_Handler {

    private $client;
    private $apiKey;

    private $isSubscribed;

    const GET_RESPONSE_CONTACT_EMAIL = 'getResponseContactEmail';


    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(GetResponseSignup_Config::GETRESPONSE_API_KEY, '');
        $context->addDbSetting(GetResponseSignup_Config::GETRESPONSE_CAMPAIGN_NAME, '');
        $context->addDbSetting(GetResponseSignup_Config::CUSTOM_DATA_FIELDS, '');
    }

    /**
     * @return GetResponseSignup_Main
     */
    public static function getHandlerInstance() {
        return new GetResponseSignup_Main();
    }

    public static function checkRequiredSettings() {
        if (!strlen(Gpf_Settings::get(GetResponseSignup_Config::GETRESPONSE_API_KEY))) {
            throw new Gpf_Exception('GetResponse Api Key not defined. Please edit api key in GetResonseSignup plugin configuration!');
        }
        if (!strlen(Gpf_Settings::get(GetResponseSignup_Config::GETRESPONSE_CAMPAIGN_NAME))) {
            throw new Gpf_Exception('GetResponse Campaign name not defined. Please edit Campaign name in GetResonseSignup plugin configuration');
        }
    }

    public function userStatusChanged(Gpf_Plugins_ValueContext $context) {
        $data = $context->get();
        $user = $data[0];
        $newStatus = $data[1];
        Gpf_Log::info('GetResponse - userStatusChanged started, status:' . $newStatus);
        $this->connect();
        $oldEmail = $this->loadContactEmail($user);
        if($newStatus == Pap_Common_Constants::STATUS_APPROVED && !$this->isSubscribed) {
            $this->signupToGetResponse($user);
            return;
        }
        if($this->isSubscribed) {
            $this->deleteContact($user, $oldEmail);
        }
    }

    public function changeEmail(Pap_Common_User $user) {
        Gpf_Log::info('GetResponse - changeEmail started');
        self::checkRequiredSettings();
        $this->connect();
        $oldEmail = $this->loadContactEmail($user);
        if(!$this->isSubscribed) {
            if($user->getStatus() == Pap_Common_Constants::STATUS_APPROVED) {
                $this->signupToGetResponse($user);
            }
            return;
        }
        Gpf_Log::info('GetResponse - Old email found: ' . $oldEmail);
        if($oldEmail == $user->getEmail() || $oldEmail == '') {
            return;
        }
        $this->deleteContact($user, $oldEmail);
        $this->signupToGetResponse($user);
    }

    public function userDeleted(Pap_Common_User $user) {
        Gpf_Log::info('GetResponse - userDeleted start.');
        $this->loadContactEmail($user);
        $this->deleteContact($user, $user->getEmail());
    }

    public function signupToGetResponse(Pap_Common_User $user) {
        Gpf_Log::info('GetResponse - Signup started');
        $this->loadContactEmail($user);
        if($this->isSubscribed) {
            Gpf_Log::info('GetResponse - user has been already saved.');
            return;
        }
        if($user->getAccountUserId() == '') {
            Gpf_Log::info('GetResponse - user has not been saved yet, returning');
            return;
        }
        $this->signup($user);
        $this->storeContactEmail($user);
        Gpf_Log::info('GetResponse - Signup end');
    }

    protected function logError($err) {
        Gpf_Log::error($err);
    }

    protected function getClientObject() {
        return new GetResponseSignup_JsonRPCClient(GetResponseSignup_Config::API_URL);
    }
    
    private function storeContactEmail($user) {
        $userAttr = $this->getUserAttributeObject();
        $userAttr->setAccountUserId($user->getAccountUserId());
        $userAttr->setName(self::GET_RESPONSE_CONTACT_EMAIL);
        $userAttr->setValue($user->getEmail());
        $userAttr->insert();
    }

    private function connect() {
        $this->client = $this->getClientObject();
        $this->apiKey = Gpf_Settings::get(GetResponseSignup_Config::GETRESPONSE_API_KEY);
    }

    private function resolveCampaignId() {
        Gpf_Log::info('GetResponse - Trying to resolve campaign ' . Gpf_Settings::get(GetResponseSignup_Config::GETRESPONSE_CAMPAIGN_NAME));
        $result = $this->callFunction('get_campaigns', array (
                    'name' => array ( 'EQUALS' => Gpf_Settings::get(GetResponseSignup_Config::GETRESPONSE_CAMPAIGN_NAME) )
        ));
        return array_pop(array_keys($result));
    }

    private function deleteContact(Pap_common_user $user, $oldEmail) {
        if(!$this->isSubscribed) {
            return;
        }
        Gpf_Log::info('GetResponse - deleteContact');
        $this->callFunction('delete_contact', array('contact' => $this->getContactId($oldEmail)));

        $userAttr = new Gpf_Db_UserAttribute();
        $userAttr->setName(self::GET_RESPONSE_CONTACT_EMAIL);
        $userAttr->setAccountUserId($user->getAccountUserId());
        $userAttr->loadFromData();
        $userAttr->delete();
    }

    private function getContactId($email) {
        $result = $this->callFunction('get_contacts', array('email' => array('EQUALS' => $email)));
        return array_pop(array_keys($result));
    }

    protected function getUserAttributeObject() {
        return new Gpf_Db_UserAttribute();
    }

    private function loadContactEmail(Pap_Common_User $user) {
        Gpf_Log::info('GetResponse - loadContactEmail from DB');
        $userAttr = $this->getUserAttributeObject();
        $userAttr->setAccountUserId($user->getAccountUserId());
        $userAttr->setName(self::GET_RESPONSE_CONTACT_EMAIL);
        $this->isSubscribed = true;
        try {
            $userAttr->loadFromData();
        } catch (Gpf_Exception $e) {
            Gpf_Log::info('GetResponse - contact not found in DB');
            $this->isSubscribed = false;
            return;
        }

        return $userAttr->getValue();
    }

    private function signup(Pap_Common_User $user) {
        self::checkRequiredSettings();
        $this->connect();
        $campaignId = $this->resolveCampaignId();

        $result = $this->callFunction('add_contact', array (
            'campaign'  => $campaignId,
            'name'      => $user->getFirstName() . ' ' . $user->getLastName(),
            'email'     => $user->getEmail(),
            'customs'   => $this->getCustomFields($user)));
        Gpf_Log::info('GetResponse - Affiliate added');
    }

    private function callFunction($functionName, $params) {
        Gpf_Log::info('GetResponse - callFunction ' . $functionName . ', params: ' . print_r($params, true));
        try {
            $result = $this->client->$functionName($this->apiKey, $params);
            Gpf_Log::info('GetResponse - callFunction result: ' . print_r($result, true));
            return $result;
        } catch (Exception $e) {
            $this->logError('GetResponse Exception - Failed to signup affiliate to GetResponse newsletter with error: ' . $e->getMessage());
        }
    }

    private function getCustomFields(Pap_Common_User $user) {
        $customFields = explode(',', Gpf_Settings::get(GetResponseSignup_Config::CUSTOM_DATA_FIELDS));

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_FormFields::NAME);
        $select->select->add(Gpf_Db_Table_FormFields::CODE);
        $select->from->add(Gpf_Db_Table_FormFields::getName());

        array_walk($customFields, create_function('&$val', '$val = trim(strtolower($val));'));
        $select->where->add(Gpf_Db_Table_FormFields::CODE, 'IN', $customFields);
        $select->where->add(Gpf_Db_Table_FormFields::FORMID, '=', Pap_Merchants_Config_AffiliateFormDefinition::FORMID);
        $customs = array();
        $x = $select->toString();
        foreach($select->getAllRows() as $row) {
            $customs[] = array(
                'name' => str_replace(' ', '_', Gpf_Lang::_localizeRuntime($row->get(Gpf_Db_Table_FormFields::NAME))),
                'content' => $user->getData(str_replace('data', '', $row->get(Gpf_Db_Table_FormFields::CODE)))
            );
        }
        return $customs;
    }
}
?>
