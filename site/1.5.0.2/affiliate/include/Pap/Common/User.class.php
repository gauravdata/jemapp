<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: User.class.php 33992 2011-07-28 09:28:05Z iivanco $
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
class Pap_Common_User extends Gpf_DbEngine_RowComposite  {

    /**
     * @var Pap_Db_User
     */
    protected $user;

    /**
     * @var Gpf_Db_User
     */
    protected $accountUser;

    /**
     * @var Gpf_Db_AuthUser
     */
    protected $authUser;

    private $inserting = false;

    private $sendNotification = true;

    public function __construct() {
        $this->user = new Pap_Db_User();
        parent::__construct($this->user);

        $this->accountUser = new Gpf_Db_User();
        $this->addRowObject($this->accountUser);

        $this->authUser = new Pap_Common_AuthUser();
        $this->addRowObject($this->authUser);

        $this->setDeleted(false);
    }

    /**
     * @return Gpf_Db_AuthUser
     */
    public function getAuthUser() {
        return $this->authUser;
    }

    public function delete() {
        $this->user->delete();
        $this->accountUser->delete();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterDelete', $this);
    }

    public function changeStatus($userid, $status) {
        $this->setPrimaryKeyValue($userid);
        $this->load();
        $this->setStatus($status);
        $this->save();
    }

    public function getStatus() {
        return $this->get(Gpf_Db_Table_Users::STATUS);
    }

    public function setStatus($status) {
        $this->set(Gpf_Db_Table_Users::STATUS, $status);
    }

    public function setAccountUserId($accountUserId) {
        $this->set(Pap_Db_Table_Users::ACCOUNTUSERID, $accountUserId);
    }

    public function setSendNotification($status) {
        $this->sendNotification = $status;
    }

    public function getSendNotification() {
        return $this->sendNotification;
    }

    public function setPayoutOptionId($payoutOptionId) {
        $this->set(Pap_Db_Table_Users::PAYOUTOPTION_ID, $payoutOptionId);
    }

    public function insert() {
        return $this->save();
    }

    public function update($updateColumns = array()) {
        return $this->save();
    }

    public function save() {
        if ($this->isFirstChangeStatus()) {
            $this->setDateApproved(Gpf_Common_DateUtils::now());
        }
        try {
            $authUser = new Gpf_Db_AuthUser();
            $authUser->setPrimaryKeyValue($this->authUser->getPrimaryKeyValue());
            $authUser->load();
            $this->accountUser->setAuthId($authUser->getId());
        } catch (Gpf_Exception $e) {
            try {
                $this->authUser->loadFromUsername();
                $this->accountUser->setAuthId($this->authUser->getId());
            } catch (Exception $e) {
            }
        }

        $this->inserting = !$this->user->rowExists();

        $this->checkConstraints();

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.beforeSave', $this);

        $this->authUser->save();
        $this->accountUser->setAuthId($this->authUser->getId());

        try {
            $this->accountUser->save();
        } catch (Gpf_Exception $e) {
            $this->authUser->delete();
            throw new Gpf_Exception($e->getMessage());
        }

        $this->user->set('accountuserid', $this->accountUser->get('accountuserid'));
        $this->initRefid($this->accountUser->getId());
        $this->initMinimupPayout();

        try {
            $this->user->save();
        } catch (Gpf_Exception $e) {
            $this->authUser->delete();
            $this->accountUser->delete();
            throw new Gpf_Exception($e->getMessage());
        }

        if($this->inserting) {
            $this->afterInsert();
        } else {
            Pap_Db_Table_CachedBanners::deleteCachedBannersForUser($this->user->getId(), $this->user->getRefId());
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterSave', $this);
        }
    }

    protected function setDefaultTheme($theme) {
        Gpf_Db_Table_UserAttributes::setSetting(Gpf_Auth_User::THEME_ATTRIBUTE_NAME, $theme, $this->getAccountUserId());
    }

    protected function setQuickLaunchSetting($setting) {
        Gpf_Db_Table_UserAttributes::setSetting('quickLaunchSetting', $setting, $this->getAccountUserId());
    }


    protected function initRefid($refid) {
    }

    protected function afterInsert() {
        $this->setupNewUser();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterInsert', $this);
    }

    protected function setupNewUser() {
    }

    public function load() {
        $this->user->load();
        $this->accountUser->set('accountuserid', $this->user->get('accountuserid'));
        $this->accountUser->load();
        $this->authUser->set('authid', $this->accountUser->get('authid'));
        $this->authUser->load();
    }

    public function loadFromData(array $loadColumns = array()) {
        $this->user->loadFromData($loadColumns);
        $this->accountUser->set('accountuserid', $this->user->get('accountuserid'));
        $this->accountUser->load();
        $this->authUser->set('authid', $this->accountUser->get('authid'));
        $this->authUser->load();
    }

    /**
     * changes status of user(s)
     *
     * @service affiliate write
     * @param ids - array of IDs
     * @param status - new status
     * @return Gpf_Rpc_Action
     */
    public function changeStatusUsers(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to change status for %s user(s)'));
        $action->setInfoMessage($this->_('Status successfully changed for %s user(s)'));

        $status = $action->getParam("status");

        if (!in_array($status, array(Gpf_Db_User::APPROVED, Gpf_Db_User::PENDING, Gpf_Db_User::DECLINED))) {
            throw new Exception($this->_("Status does not have allowed value"));
        }


        foreach ($action->getIds() as $userid) {
            try {
                $result = $this->changeStatus($userid, $status);
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }

    /**
     * For compatibility with FormService
     *
     * @param string $id
     */
    public function setPrimaryKeyValue($id) {
        $this->setId($id);
    }

    /**
     * For compatibility with FormService
     */
    public function getPrimaryKeyValue() {
        return $this->getId();
    }

    /**
     * @return string PAP user id
     */
    public function getId() {
        return $this->user->getId();
    }

    public function getParentUserId() {
        return $this->user->getParentUserId();
    }

    public function setParentUserId($userid) {
        $this->user->setParentUserId($userid);
    }

    public function setOriginalParentUserId($userid) {
        $this->user->setOriginalParentUserId($userid);
    }

    public function setId($id) {
        return $this->user->setId($id);
    }

    public function setPassword($password) {
        $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, $password);
    }

    public function setUserName($username) {
        $this->set(Gpf_Db_Table_AuthUsers::USERNAME, $username);
    }

    public function setFirstName($firstName) {
        $this->set(Gpf_Db_Table_AuthUsers::FIRSTNAME, $firstName);
    }

    public function setLastName($lastName) {
        $this->set(Gpf_Db_Table_AuthUsers::LASTNAME, $lastName);
    }

    public function setDateInserted($value) {
        $this->set(Pap_Db_Table_Users::DATEINSERTED, $value);
    }

    public function setDateApproved($value) {
        $this->set(Pap_Db_Table_Users::DATEAPPROVED, $value);
    }

    public function getDateApproved() {
        return $this->get(Pap_Db_Table_Users::DATEAPPROVED);
    }

    public function setAccountId($accountId) {
        $this->accountUser->setAccountId($accountId);
    }

    public function setRoleId($roleId) {
        $this->accountUser->setRoleId($roleId);
    }

    public function getRoleId() {
        return $this->accountUser->getRoleId();
    }

    public function setRefId($refId) {
        $this->set('refid', $refId);
    }

    public function setNote($value) {
        $this->user->set(Pap_Db_Table_Users::NOTE, $value);
    }

    public function setPhoto($value) {
        $this->user->set(Pap_Db_Table_Users::PHOTO, $value);
    }

    public function setData($i, $value) {
        $this->user->set('data'.$i, $value);
    }

    public function getData($i) {
        return $this->user->get('data'.$i);
    }

    public function getRefId() {
        $refId = $this->get('refid');
        if($refId != null && $refId != '') {
            return $refId;
        }
        return $this->getId();
    }

    public function getName() {
        return $this->get('firstname')." ".$this->get('lastname');
    }

    public function setIp($ip) {
        return $this->authUser->setIp($ip);
    }

    public function getIp() {
        return $this->authUser->getIp();
    }

    public function getUserName() {
        return $this->get('username');
    }

    public function getFirstName() {
        return $this->get(Gpf_Db_Table_AuthUsers::FIRSTNAME);
    }

    public function getLastName() {
        return $this->get(Gpf_Db_Table_AuthUsers::LASTNAME);
    }

    public function getEmail() {
        return $this->authUser->getEmail();
    }

    public function setEmail($email) {
        $this->authUser->setEmail($email);
    }

    public function getType() {
        return $this->get(Pap_Db_Table_Users::TYPE);
    }

    public function getPassword() {
        return $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
    }

    public function getAccountUserId() {
        return $this->accountUser->getPrimaryKeyValue();
    }

    public function getAccountId() {
        return $this->accountUser->getAccountId();
    }

    public function getPayoutOptionId() {
        return $this->user->getPayoutOptionId();
    }

    public function getMinimumPayout() {
        return $this->user->getMinimumPayout();
    }

    public function setType($type) {
        $this->set(Pap_Db_Table_Users::TYPE, $type);
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted) {
        if ($deleted == false) {
            $this->user->setDeleted(Gpf::NO);
        } else {
            $this->user->setDeleted(Gpf::YES);
        }
    }

    public function getDeleted(){
        return $this->user->getDeleted();
    }

    public function setMinimumPayout($minimumPayout) {
        $this->user->setMinimumPayout($minimumPayout);
    }

    /**
     * @return Pap_Common_User or null
     */
    public function getParentUser() {
        $parentUserId = $this->getParentUserId();
        if($parentUserId == '') {
            return null;
        }

        $objUser = new Pap_Common_User();
        $objUser->setPrimaryKeyValue($parentUserId);
        try {
            $objUser->load();
            return $objUser;
        } catch (Gpf_DbEngine_NoRowException $e) {
            return null;
        }
    }

    public static function getMerchantEmail() {
        return Gpf_Settings::get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL);
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $userId
     * @return Pap_Common_User
     */
    public static function getUserById($userId) {
        $user = new Pap_Common_User();
        $user->setId($userId);
        $user->load();
        return $user;
    }

    protected function addGadget($type, $name, $url, $posType, $top, $left, $width, $height) {
        $gadgetManager = new Gpf_GadgetManager();
        $gadget = $gadgetManager->addGadgetNoRpc($name, $url, $posType, $this->getAccountUserId());
        $gadget->setType($type);
        $gadget->setPositionTop($top);
        $gadget->setPositionLeft($left);
        $gadget->setWidth($width);
        $gadget->setHeight($height);
        $gadget->save();
    }

    private function initMinimupPayout() {
        if ($this->inserting) {
            $this->user->setMinimumPayout(Gpf_Settings::get(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME));
        }
    }

    public function getNumberOfUsersFromSameIP($ip, $periodInSeconds) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add("count(au.authid)", "count");
        $select->from->add(Pap_Db_Table_Users::getName(),'pu');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(),'qu','pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=qu.'.Gpf_Db_Table_Users::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),'au','au.'.Gpf_Db_Table_AuthUsers::ID.'=qu.'.Gpf_Db_Table_Users::AUTHID. ' and qu.'.Gpf_Db_Table_Users::ROLEID."='".Pap_Application::DEFAULT_ROLE_AFFILIATE."'");
        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::IP, "=", $ip);
        $dateFrom = new Gpf_DateTime();
        $dateFrom->addSecond(-1*$periodInSeconds);
        $select->where->add(Pap_Db_Table_Users::DATEINSERTED, ">", $dateFrom->toDateTime());

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->load($select);

        foreach($recordSet as $record) {
            return $record->get("count");
        }
        return 0;
    }

    /**
     * @return boolean
     */
    public function isFirstChangeStatus() {
        $firstChange = false;
        if ($this->getStatus() !== Pap_Common_Constants::STATUS_PENDING &&
        ($this->getDateApproved() === null || $this->getDateApproved() === "")) {
            $firstChange = true;
        }
        return $firstChange;
    }

    public function getOriginalParentUserId() {
        return $this->user->getOriginalParentUserId();
    }

    private function checkConstraints() {
        try{
            $this->check();
        } catch (Gpf_DbEngine_Row_CheckException $e) {
            $exceptions = array();
            foreach ($e as $constraintExeption) {
                if($constraintExeption instanceof Gpf_DbEngine_Row_PasswordConstraintException && $this->accountUser->getRoleId() == Pap_Application::DEFAULT_ROLE_MERCHANT) {
                    continue;
                }
                $exceptions[] = $constraintExeption;
            }
            if(count($exceptions) > 0) {
                throw new Gpf_DbEngine_Row_CheckException($exceptions);
            }
        }
    }
}

?>
