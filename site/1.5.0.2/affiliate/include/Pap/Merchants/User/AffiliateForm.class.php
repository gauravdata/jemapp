<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 33732 2011-07-11 11:35:10Z iivanco $
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
class Pap_Merchants_User_AffiliateForm extends Pap_Common_UserForm {
    const PAYOUT_OPTION = "payout_option_";
    const DEFAULT_OVERWRITE_COOKIE = "D";

    /**
     * @var Gpf_Db_Table_UserAttributes
     */
    private $attribute;

    public function __construct() {
        $this->attribute = Gpf_Db_Table_UserAttributes::getInstance();
    }

    /**
     *
     * @service affiliate read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);

        return $form;
    }

    /**
     * @return Pap_Affiliates_User
     */
    protected function createDbRowObject() {
        $this->user = new Pap_Affiliates_User();
        return $this->user;
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Affiliate");
    }

    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues($dbRow) {
        $dbRow->set("accountid", Gpf_Session::getAuthUser()->getAccountId());
        $dbRow->set("numberuserid", 1);
        $dbRow->set("rtype", Pap_Application::ROLETYPE_AFFILIATE);

        $approvalType = Gpf_Settings::get(Pap_Settings::AFFILIATE_APPROVAL);
        if($approvalType == 'A') {
            $dbRow->set("rstatus", Gpf_Db_User::APPROVED);
        } else {
            $dbRow->set("rstatus", Gpf_Db_User::PENDING);
        }
        $dbRow->set("deleted", Gpf::NO);
        $dbRow->set("dateinserted", Gpf_Common_DateUtils::Now());
        $dbRow->set("refid", uniqid());
        $dbRow->set("rpassword", Gpf_Common_String::generatePassword(8));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateForm.setDefaultDbRowObjectValues', $dbRow);
    }

    /**
     * called after the object is saved
     *
     * @param unknown_type $dbRow
     */
    protected function afterSave($dbRow, $saveType) {
        if($saveType != Gpf_View_FormService::ADD) {
            return;
        }

        // set default email notifications settings
        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_new_sale",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_change_comm_status",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_subaff_signup",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_subaff_sale",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_direct_link_enabled",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_daily_report",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_weekly_report",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT),
        $dbRow->getAccountUserId());

        Gpf_Db_Table_UserAttributes::setSetting("aff_notification_monthly_report",
        Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT),
        $dbRow->getAccountUserId());

        $this->user = $dbRow;
    }

    /**
     * special handling - if password is empty, don't save it
     *
     * @service affiliate write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }

        $oldPassword = $dbRow->getPassword();
        $form->fill($dbRow);
        $newPassword = $dbRow->getPassword();

        if($newPassword == '') {
            $dbRow->setPassword($oldPassword);
        }

        if(!$this->checkBeforeSave($dbRow, $form, self::EDIT)) {
            return $form;
        }
        try {
            $dbRow->save();
            $this->afterSave($dbRow, self::EDIT);
        } catch (Gpf_DbEngine_Row_CheckException $checkException) {
            foreach ($checkException as $contstraintException) {
                if ($form->existsField($contstraintException->getFieldCode())) {
                    $form->setFieldError($contstraintException->getFieldCode(), $contstraintException->getMessage());
                }
            }
            $form->setErrorMessage($checkException->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($dbRow);
        $form->setInfoMessage($this->getDbRowObjectName().$this->_(" saved"));
        return $form;
    }

    protected function getDefaultUserRole() {
        return Pap_Application::DEFAULT_ROLE_AFFILIATE;
    }

    protected function checkBeforeSave($row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
        $result = true;
        $result = $this->checkParentUserIsValid($row, $form, $operationType) && $result;
        $result = $this->checkRefidIsValid($row, $form, $operationType) && $result;
        $result = $this->checkUsernameIsValidEmail($form, $operationType) && $result;
        $result = $this->checkUsernameIsUnique($form, $operationType) && $result;
        $result = $this->checkUserCanChangeUsername($row, $form, $operationType) && $result;
        $result = $this->checkFormContainsMandatoryFields($form, $operationType) && $result;
        $result = $this->checkAgreeChecked($form, $operationType) && $result;
        return $result;
    }

    protected function checkUsernameIsUnique(Gpf_Rpc_Form $form, $operationType) {

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('count(au.'.Gpf_Db_Table_AuthUsers::USERNAME.')','numberOfUsernames');
        $select->from->add(Gpf_Db_Table_AuthUsers::getName(),'au');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "gu.authid = au.authid");
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "pu.accountuserid = gu.accountuserid");
        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME,'=',$form->getFieldValue('username'));
        if($operationType == self::EDIT) {
            $select->where->add('pu.'.Pap_Db_Table_Users::ID,'!=', $this->getId($form));
        }
        $row = $select->getOneRow();
        if($row->get('numberOfUsernames') == 0) {
            return true;
        }

        $form->setErrorMessage($this->_("Username is already used by another user."));
        return false;
    }

    private function checkUserCanChangeUsername($row, Gpf_Rpc_Form $form) {
        if (Gpf_Session::getRoleType() == Pap_Application::ROLETYPE_MERCHANT || $form->getFieldValue('username') == $row->get('username') || Gpf_Settings::get(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME) == Gpf::NO) {
            return true;
        }
        return false;
    }

    private function checkRefidIsValid($row, Gpf_Rpc_Form $form, $operationType) {
        if ($form->existsField("refid") && $form->getFieldValue("refid") == "") {
            $form->setField("refid", uniqid());
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateForm.checkRefidIsValid', $form);
            $row->set("refid", $form->getFieldValue("refid"));
        }
        return true;
    }

    private function checkParentUserIsValid($row, Gpf_Rpc_Form $form, $operationType) {
        if (Gpf_Settings::get(Pap_Settings::NOT_SET_PARENT_AFFILIATE) == Gpf::YES) {
            $this->setParentUserId($row, '');
            return true;
        }

        if (!$form->existsField("parentuserid") || $form->getFieldValue("parentuserid") == "") {
            if (!$form->existsField('cookieParentUserId') || $form->getFieldValue('cookieParentUserId') == '') {
                return true;
            }
            $parentUserId = $form->getFieldValue('cookieParentUserId');
            $parentUserIdDoNotValidate = true;
        } else {
            $parentUserId = $form->getFieldValue("parentuserid");
            $parentUserIdDoNotValidate = $form->existsField("parentUserIdValidate") && $form->getFieldValue("parentUserIdValidate") == "N";
        }

        try {
            $affiliate = Pap_Affiliates_User::loadFromId($parentUserId);
            $this->setParentUserId($row, $affiliate->getId());
            $this->setOriginalParentUserId($row, $affiliate->getId());
        } catch (Gpf_Exception $e) {
            if ($parentUserIdDoNotValidate) {
                $this->setParentUserId($row, '');
                return true;
            } else {
                $form->setErrorMessage($this->_("Selected parent user does not exist"));
                return false;
            }
        }
        if ($operationType == self::ADD) {
            return true;
        }
        $result = $this->checkForCycle($affiliate, $this->getId($form));
        if ($result==false){
            $form->setErrorMessage($this->_("Selected parent can not be used!"));
        }
        return $result;
    }

    protected function checkForCycle(Pap_Affiliates_User $potentialParent, $userId) {
        $tree = new Pap_Common_UserTree();
        $tree->startCheckingLoops();
        $affiliate = $potentialParent;
        while ($affiliate != null){
            if ($userId == $affiliate->getId()) {
                return false;
            }
            $affiliate = $tree->getParent($affiliate);
        }
        return true;
    }

    private function setParentUserId(Pap_Common_User $user, $parentUserId) {
        $user->setParentUserId($parentUserId);
    }

    private function setOriginalParentUserId(Pap_Common_User $user, $parentUserId) {
        $user->setOriginalParentUserId($parentUserId);
    }

    private function checkAgreeChecked(Gpf_Rpc_Form $form, $operationType) {
        if ($operationType == self::EDIT) {
            return true;
        }
        $forceAcceptance = Gpf_Settings::get(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME);
        if ($forceAcceptance == "N") {
            return true;
        }
        try {
            if ($form->getFieldValue("agreeWithTerms") == Gpf::YES) {
                return true;
            }
            $form->setFieldError("agreeWithTerms", $this->_("You have to agree with terms & conditions"));
        } catch (Gpf_Exception $e) {
            $form->setField("agreeWithTerms", "N", null, $this->_("You have to agree with terms & conditions"));
        }
        return false;
    }

    private function checkFormContainsMandatoryFields(Gpf_Rpc_Form $form, $operationType) {
        $result = true;
        $mandatoryFields = array('username' => $this->_("Username (Email)"),
                                 'firstname' => $this->_("First name"),
                                 'lastname' => $this->_("Last name"));
        $formFields = Gpf_Db_Table_FormFields::getInstance()->getFieldsNoRpc(
                            "affiliateForm",
        Gpf_Db_FormField::STATUS_MANDATORY);

        $this->setMandatoryFields($formFields, $mandatoryFields);

        $field = new Gpf_Db_FormField();
        $field->setStatus(Gpf_Db_FormField::STATUS_MANDATORY);
        $field->setFormId(Pap_Merchants_Config_AffiliateFormDefinition::FORMID);
        try {
            $result = $field->loadCollection(array(Gpf_Db_Table_FormFields::STATUS, Gpf_Db_Table_FormFields::FORMID));
            foreach($result as $f) {
                $mandatoryFields[$f->get(Gpf_Db_Table_FormFields::CODE)] = $f->get(Gpf_Db_Table_FormFields::NAME);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
        }

        $cookieParentUserId = '';
        try {
            $cookieParentUserId = $form->getFieldValue('cookieParentUserId');
        } catch (Gpf_Data_RecordSetNoRowException $e) {
        }

        foreach ($mandatoryFields as $fieldCode => $fieldName) {
            if($fieldCode == Pap_Db_Table_Users::PARENTUSERID && $cookieParentUserId != '') {
                continue;
            }
            try {
                if ($form->getFieldValue($fieldCode) == "") {
                    throw new Gpf_Exception("");
                }
            } catch (Gpf_Exception $e) {
                try {
                    $this->setFormFieldError($form, $fieldCode, $fieldName);
                } catch (Gpf_Data_RecordSetNoRowException $e) {
                    $form->addField($fieldCode, '');
                    $this->setFormFieldError($form, $fieldCode, $fieldName);
                }
                $result = false;
            }
        }
        return $result;
    }

    private function setFormFieldError($form, $fieldCode, $fieldName) {
        $form->setFieldError($fieldCode, $fieldName . $this->_("&nbsp;is mandatory"));
    }

    protected function setMandatoryFields(Gpf_Data_RecordSet $formFields, array $mandatoryFields) {
        foreach ($formFields as $field) {
            $mandatoryFields[$field->get("code")] = $this->_localize($field->get("name"));
        }
    }

    /**
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function loadTracking(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        $this->attribute->loadAttributes($this->user->getAccountUserId());
        $form = $this->loadTrackingOption($form);
        return $form;
    }

    /**
     * @service affiliate write
     * @param Gpf_Rpc_Params $params
     */
    public function saveTracking(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }

        $this->saveTrackingOption($form);

        $form->setInfoMessage($this->_("Tracking options saved"));

        return $form;
    }

    protected function getId(Gpf_Rpc_Form $form) {
        if ($form->getFieldValue("Id") == "") {
            return Gpf_Session::getAuthUser()->getPapUserId();
        }
        return parent::getId($form);
    }


    /**
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function loadPayouts(Gpf_Rpc_Params $params) {
        $form = $this->load($params);
        $this->attribute->loadAttributes($this->user->getAccountUserId());
        $form = $this->loadPayoutSettings($form);
        return $form;
    }

    /**
     * @service affiliate write
     * @param Gpf_Rpc_Params $params
     */
    public function savePayouts(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->setField("Id", $this->getId($form));

        $dbRow = $this->createDbRowObject();

        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }

        $form->fill($dbRow);

        try {
            $dbRow->save();
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $this->savePayoutSettings($form);
        return $form;
    }

    /**
     * @anonym
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function loadPayoutFields(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        if (!Gpf_Session::getAuthUser()->isLogged()) {
            return $form;
        }

        $this->user = $this->createDbRowObject();
        $this->user->setPrimaryKeyValue($this->getId($form));
        try {
            $this->user->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->getDbRowObjectName().$this->_(" does not exist"));
        }

        $this->attribute->loadAttributes($this->user->getAccountUserId());

        $payoutOptionId = $form->getFieldValue("payoutOptionId");
        if ($payoutOptionId == null) {
            return $form;
        }
        $formFields = Gpf_Db_Table_FormFields::getInstance();
        $formName = "payout_option_" . $payoutOptionId;
        $payoutOptionFields = $formFields->getFieldsNoRpc($formName);

        $payoutOptionValues = Pap_Db_Table_UserPayoutOptions::getInstance()->getValues($formName, $this->user->getId());
        foreach ($payoutOptionFields as $field) {
            $code = $field->get("code");
            if (array_key_exists($code, $payoutOptionValues)) {
                $value = $payoutOptionValues[$code]->getValue();
            } else {
                $value = '';
            }
            $form->setField($code, $value);
        }

        return $form;
    }

    /**
     *
     * @service affiliate write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params, $this->_("Selected affiliate(s) status is changed"),$this->_("Failed to change status for selected affiliate(s)"));
        $sendNotification = true;
        if ($action->getParam('dontSendNotification') == Gpf::YES) {
            $sendNotification = false;
        }

        foreach ($action->getIds() as $id){
            try {
                $user = $this->createDbRowObject();
                $user->setId($id);
                $user->load();
                if ($user->getStatus() == $action->getParam("status")) {
                    continue;
                }
                $user->setStatus($action->getParam("status"));
                $user->setSendNotification($sendNotification);
                $user->save();
                $action->addOk();
            } catch(Gpf_DbEngine_NoRowException $e) {
                $action->addError();
            }
        }

        return $action;
    }

    private function savePayoutSettings(Gpf_Rpc_Form $form) {
        $this->savePayoutFields($form);

        $accountUserId = $this->user->getAccountUserId();
        Gpf_Db_Table_UserAttributes::setSetting("apply_vat_invoicing", $this->getFieldValue($form, "applyVatInvoicing"), $accountUserId);
        Gpf_Db_Table_UserAttributes::setSetting("vat_number", $this->getFieldValue($form, "vatNumber"), $accountUserId);
        Gpf_Db_Table_UserAttributes::setSetting("amount_of_reg_capital", $this->getFieldValue($form, "amountOfRegCapital"), $accountUserId);
        Gpf_Db_Table_UserAttributes::setSetting("reg_number", $this->getFieldValue($form, "regNumber"), $accountUserId);
        Gpf_Db_Table_UserAttributes::setSetting(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME, $this->getFieldValue($form, "vatPercentage"), $accountUserId);
    }

    private function getFieldValue(Gpf_Rpc_Form $form, $fieldName) {
        if($form->existsField($fieldName)) {
            return $form->getFieldValue($fieldName);
        }
        return '';
    }

    private function saveTrackingOption(Gpf_Rpc_Form $form) {
        Gpf_Db_Table_UserAttributes::setSetting(Pap_Settings::OVERWRITE_COOKIE, $form->getFieldValue("overwriteCookie"), $this->user->getAccountUserId());
    }

    private function loadTrackingOption(Gpf_Rpc_Form $form) {
        $form->setField("overwriteCookie", $this->attribute->getAttributeWithDefaultValue(Pap_Settings::OVERWRITE_COOKIE, self::DEFAULT_OVERWRITE_COOKIE));

        return $form;
    }

    private function loadPayoutSettings(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::SUPPORT_VAT_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME));

        $form->setField("applyVatInvoicing", $this->attribute->getAttributeWithDefaultValue("apply_vat_invoicing", ""));

        $defaultVatPercentage = Gpf_Settings::get(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME);
        $vatPercentage = $this->attribute->getAttributeWithDefaultValue(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME, "");
        if($vatPercentage == '' || $vatPercentage == 0) {
            $vatPercentage = $defaultVatPercentage;
        }
        $form->setField("vatPercentage", $vatPercentage);

        $form->setField("vatNumber", $this->attribute->getAttributeWithDefaultValue("vat_number", ""));
        $form->setField("amountOfRegCapital", $this->attribute->getAttributeWithDefaultValue("amount_of_reg_capital", ""));
        $form->setField("regNumber", $this->attribute->getAttributeWithDefaultValue("reg_number", ""));
        $form->setField("currency", $this->attribute->getAttributeWithDefaultValue("currency_id", ""));

        $form->setField("minimumPayoutOptions", Gpf_Settings::get(Pap_Settings::PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME));
        $form->setField("minimumpayout", $this->getAffiliate()->getMinimumPayout());

        return $form;
    }

    protected function savePayoutFields(Gpf_Rpc_Form $form) {
        $this->user = $this->createDbRowObject();
        $this->user->setPrimaryKeyValue($form->getFieldValue("Id"));
        try {
            $this->user->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->getDbRowObjectName().$this->_(" does not exist"));
        }

        $payoutOptionId = $form->getFieldValue(Pap_Db_Table_Users::PAYOUTOPTION_ID);
        $formFields = Gpf_Db_Table_FormFields::getInstance();
        $formName = self::PAYOUT_OPTION . $payoutOptionId;
        $payoutOptionFields = $formFields->getFieldsNoRpc($formName);

        foreach ($payoutOptionFields as $field) {
            $payoutOptionUserValue = new Pap_Db_UserPayoutOption();
            $payoutOptionUserValue->setUserId($this->user->getId());
            $payoutOptionUserValue->setFormFieldId($field->get("id"));
            $payoutOptionUserValue->setValue($form->getFieldValue($field->get("code")));
            $payoutOptionUserValue->save();
        }

        $form->setInfoMessage($this->_("Payout settings saved"));
        return $form;
    }

    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        if ($form->existsField("dontSendEmail")) {
            $dbRow->setSendNotification('N' == $form->getFieldValue('dontSendEmail'));
        }
        parent::fillAdd($form, $dbRow);
    }

    protected function addRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateForm.fillAdd', $row);
        $this->assignParent($row);
        $row->insert();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateForm.afterSave', $row);
        if ($form->existsField('createSignupReferralComm') && $form->getFieldValue('createSignupReferralComm') == Gpf::YES) {
            $this->addSignupCommissions($row);
        }
    }

    /**
     * @return Pap_Common_User
     */
    public function getAffiliate() {
        return $this->user;
    }

    /**
     * @service affiliate add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }

    /**
     * @service affiliate write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service affiliate delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params, $this->_('%s row(s) successfully deleted'), $this->_('Failed to delete %s row(s)'));
        $moveChildAffiliates = $action->getParam("moveChildAffiliates") == "Y";

        foreach ($action->getIds() as $id) {
            try {
                $row = $this->createDbRowObject();
                $row->setPrimaryKeyValue($id);
                $row->delete($moveChildAffiliates);
                $action->addOk();
            } catch (Exception $e) {
                print_r($e);
                $action->addError();
            }
        }

        return $action;
    }

    /**
     * @service affiliate write
     * @return Gpf_Rpc_Action
     */
    public function sendSignupConfirmation(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params, $this->_('Signup confirmation email successfully sent'),$this->_('Failed to send signup confirmation email'));
        $mail = new Pap_Mail_NewUserSignupApproved();

        foreach ($action->getIds() as $id) {
            try {
                $user = $this->loadUserFromId($id);
                $mail->setUser($user);
                $this->sendMail($user->getAuthUser(), $mail);
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }

    /**
     * @param $id
     * @return Pap_Affiliates_User
     */
    private function loadUserFromId($id){
        $user = $this->createDbRowObject();
        $user->setPrimaryKeyValue($id);
        $user->load();
        return $user;
    }

    private function sendMail(Gpf_Db_AuthUser $user, Gpf_Mail_Template $mail) {
        $userMail = clone $mail;
        $userMail->addRecipient($user->getEmail());
        $userMail->sendNow();
    }

    /**
     * @service affiliate write
     * @return Gpf_Rpc_Action
     */
    public function sendRequestPassword(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params, $this->_('New password request email successfully sent'), $this->_('Failed to send new password request email'));

        foreach ($action->getIds() as $id) {
            try {
                $affiliate = $this->loadUserFromId($id);
                if ($affiliate->getStatus() == Pap_Common_Constants::STATUS_APPROVED) {
                    $user = $affiliate->getAuthUser();
                    $mail = new Gpf_Auth_RequestNewPasswordMail();
                    $mail->setUser($user);
                    $mail->setUrl(Gpf_Paths::getInstance()->getFullBaseServerUrl() . "affiliates/login.php");
                    $this->sendMail($user, $mail);
                    $action->addOk();
                } else {
                    $action->addError();
                }
            } catch (Exception $e) {
                $action->addError();
            }
        }

        return $action;
    }

    private function assignParent(Pap_Affiliates_User $affiliate) {
        if ($affiliate->getParentUser() == null && ($parentUserId = Gpf_Settings::get(Pap_Settings::ASSIGN_NON_REFERRED_AFFILIATE_TO)) != '') {
            try {
                $parentUser = new Pap_Affiliates_User();
                $parentUser->setId($parentUserId);
                $parentUser->load();
                $affiliate->setParentUserId($parentUser->getId());
            } catch (Gpf_Exception $e) {
            }
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateForm.assignParent', $affiliate);
        }
    }

    protected function addSignupCommissions(Pap_Affiliates_User $affiliate) {
        $bonusValue = Gpf_Settings::get(Pap_Settings::SIGNUP_BONUS);
        if ($bonusValue != 0) {
            $this->insertTransaction($affiliate->getId(), Pap_Db_Transaction::TYPE_SIGNUP_BONUS,
            $bonusValue, $affiliate->getStatus());
        }
        $this->addReferralCommissions($affiliate);
    }

    protected function addReferralCommissions(Pap_Affiliates_User $affiliate) {
        try {
            $commissionType = Pap_Db_Table_CommissionTypes::getReferralCommissionType();
        } catch (Gpf_Exception $e) {
            return;
        }
        $referralCommissions = Pap_Db_Table_Commissions::getReferralCommissions();
        if ($commissionType->getStatus() == 'D' || $referralCommissions->getSize() < 1) {
            return;
        }
        if ($commissionType->getApproval() == Pap_Db_CommissionType::APPROVAL_MANUAL) {
            $status = 'P';
        } else {
            $status = $affiliate->getStatus();
        }

        $saveZeroCommissions = $commissionType->getSaveZeroCommissions();

        $iterator = $referralCommissions->getIterator();

        while (($affiliate = $affiliate->getParentUser()) !== null) {
            if ($iterator->valid()) {
                $commission = $iterator->current();
                $this->addTransaction($affiliate->getId(), Pap_Db_Transaction::TYPE_REFERRAL, $commission->get(Pap_Db_Table_Commissions::VALUE), $status, $commission,$saveZeroCommissions);
                $iterator->next();
            } else {
                break;
            }
        }
    }

    protected function insertTransaction($affiliateId, $type, $commissionValue, $status, Gpf_Data_Record $commission = null) {
        if ($affiliateId == null) {
            return;
        }
        $transaction = new Pap_Common_Transaction();
        $transaction->setUserId($affiliateId);
        $transaction->setType($type);
        if ($commission != null) {
            $transaction->setTier($commission->get(Pap_Db_Table_Commissions::TIER));
        } else {
            $transaction->setTier('1');
        }
        $transaction->setStatus($status);
        $transaction->setPayoutStatus('U');
        $transaction->setDateInserted(Gpf_Common_DateUtils::now());
        $transaction->setCommission($commissionValue);
        $transaction->setData5($this->user->getId());
        $transaction->setIp(Gpf_Http::getRemoteIp());
        $transaction->insert();
    }

    protected function addTransaction($affiliateId, $type, $commissionValue, $status, Gpf_Data_Record $commission = null, $saveZeroCommissions = null) {
        if ($commissionValue < 0) {
            return;
        }
        if ($commissionValue == 0 && $saveZeroCommissions != Gpf::YES) {
            return;
        }
        $this->insertTransaction($affiliateId, $type, $commissionValue, $status, $commission);
    }
}

?>
