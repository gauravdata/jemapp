<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: User.class.php 17743 2008-05-06 08:25:49Z mfric $
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
class Pap_Affiliates_User extends Pap_Common_User  {

    public function __construct() {
        parent::__construct();
        $this->setType(Pap_Application::ROLETYPE_AFFILIATE);
        $this->setRoleId(Pap_Application::DEFAULT_ROLE_AFFILIATE);
    }
    
    public function set($name, $value) {
        if($name == Gpf_Db_Table_Users::STATUS && ($this->getStatus() != $value)) {
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.affiliate.userStatusChanged', new Gpf_Plugins_ValueContext(array($this, $value)));
        }
        
        parent::set($name, $value);
    }

    protected function setupNewUser() {
        $this->setDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME));
        $this->setQuickLaunchSetting('showDesktop,Home,Campaigns-List-Wide,Promo-Materials,Trends-Report');
        $this->addDefaultGadgets();
    }

    private function addDefaultGadgets() {
        $this->addGadget('C', Gpf_Lang::_runtime('Payout'), 'content://PayoutGadget',
        Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 63, 1000, 333, 92);
    }

    private function sendUserEmails() {
        if ($this->getStatus() == Pap_Common_Constants::STATUS_APPROVED) {
            $this->sendNewUserSignupApprovedMail();
        } else if ($this->getStatus() == Pap_Common_Constants::STATUS_DECLINED) {
            $this->sendNewUserSignupDeclinedMail();
        }
    }

    protected function sendNewUserSignupApprovedMail() {
        $signupEmail = new Pap_Signup_SendEmailToUser();
        $signupEmail->sendNewUserSignupApprovedMail($this, $this->getEmail());
    }

    protected function sendNewUserSignupDeclinedMail() {
        $signupEmail = new Pap_Signup_SendEmailToUser();
        $signupEmail->sendNewUserSignupDeclinedMail($this, $this->getEmail());
    }

    public function check() {
        if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
            if ($this->getUserName() != Pap_Branding::DEMO_AFFILIATE_USERNAME ||
            $this->getPassword() != Pap_Branding::DEMO_PASSWORD ||
            $this->getStatus() != "A") {
                throw new Gpf_Exception("Demo affiliate username, password and status can not be modified");
            }
        }
        return parent::check();
    }

    public function save() {
        $firstChange = $this->isFirstChangeStatus();

        parent::save();
        
        if ($firstChange && $this->getSendNotification()) {
            $this->sendUserEmails();
        }
        if ($firstChange && $this->getStatus() == Gpf_Db_User::APPROVED) {
            $this->updateStatusSignupAndReferral();
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.affiliate.firsttimeApproved', $this);
        }
        
    }

    protected function updateStatusSignupAndReferral() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Transactions::getName());
        $update->set->add(Pap_Db_Table_Transactions::R_STATUS, Pap_Common_Constants::STATUS_APPROVED);
        $update->where->add(Pap_Db_Table_Transactions::DATA5, '=', $this->getId());
        $update->where->add(Pap_Db_Table_Transactions::R_STATUS, '=', Pap_Common_Constants::STATUS_PENDING);
        $typeWhere = new Gpf_SqlBuilder_CompoundWhereCondition();
        $typeWhere->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_SIGNUP_BONUS, 'OR');
        $typeWhere->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_REFERRAL, 'OR');
        $update->where->addCondition($typeWhere);
        $update->execute();
    }

    public function delete($moveChildAffiliates = false) {
        if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
            throw new Gpf_Exception("Demo affiliate can not be deleted");
        }
        $this->load();
        if ($moveChildAffiliates) {
            $this->moveChildAffiliatesTo($this->getParentUserId());
        } else {
            $this->clearParentAffiliate();
        }
        return parent::delete();
    }

    private function moveChildAffiliatesTo($toAffiliateId) {
        $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
        $updateBuilder->from->add(Pap_Db_Table_Users::getName());
        $updateBuilder->set->add(Pap_Db_Table_Users::PARENTUSERID, $toAffiliateId);
        $updateBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, "=", $this->getId());
        $updateBuilder->execute();
    }

    private function clearParentAffiliate() {
        $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
        $updateBuilder->from->add(Pap_Db_Table_Users::getName());
        $updateBuilder->set->add(Pap_Db_Table_Users::PARENTUSERID, "");
        $updateBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, "=", $this->getId());
        $updateBuilder->execute();
    }

    /**
     * gets user by refid or userid
     * @param $id
     * @return Pap_Affiliates_User
     * @throws Gpf_Exception
     */
    public static function loadFromId($id) {
        try {
            return self::loadFromRefid($id);
        } catch (Gpf_Exception $e) {
            return self::loadFromUserid($id);
        }
    }

    /**
     * @param $userid
     * @return Pap_Affiliates_User
     * @throws Gpf_Exception
     */
    private static function loadFromUserid($userid) {
        $user = new Pap_Affiliates_User();
        $user->setPrimaryKeyValue($userid);
        $user->load();
        return $user;
    }

    /**
     * @param $refid
     * @return Pap_Affiliates_User
     * @throws Gpf_Exception
     */
    private static function loadFromRefid($refid) {
        $user = new Pap_Affiliates_User();
        $user->setRefId($refid);
        $user->loadFromData(array(Pap_Db_Table_Users::REFID));
        return $user;
    }
}

?>
