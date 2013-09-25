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
class Pap_Signup_AffiliateForm extends Pap_Merchants_User_AffiliateForm {

    const PASSWORD_LENGTH = 8;

    /**
     * @anonym
     * @service
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $context = Pap_Contexts_Signup::getContextInstance();
        $context->debug('    Saving user started');
        $context->set('params', $params);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.signup.before', $context);
        $context->setParametersObject($params);

        $form = $this->addUser($params);
        $context->setUserObject($this->getAffiliate());
        $context->setFormObject($form);

        if ((!$form->isSuccessful()) || ($this->getAffiliate() == null)) {
            $context->debug('        STOPPING, Error saving new user - did not pass the validation? Error message: '.$form->getErrorMessage());
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.signup.afterFail', $context);
        } else {
            $context->setUserObject($this->getAffiliate());
            $this->sendEmails($context);
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.signup.after', $context);
        }

        $context->debug("Signup processing ended");

        return $form;
    }
    
    protected function addUser($params) {
        return parent::add($params);
    }



    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues($dbRow) {
        parent::setDefaultDbRowObjectValues($dbRow);
        $dbRow->setPassword(Gpf_Common_String::generatePassword(8));
    }

    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        try {
            $form->getFieldValue('payoutoptionid');
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $form->setField("payoutoptionid", Gpf_Settings::get(Pap_Settings::DEFAULT_PAYOUT_METHOD));
        }
        $form->setField('createSignupReferralComm', Gpf::YES);
        parent::fillAdd($form, $dbRow);
    }

    protected function addRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
        parent::addRow($form, $row);

        $payoutOptionId = $form->getFieldValue(Pap_Db_Table_Users::PAYOUTOPTION_ID);
        if ($payoutOptionId != null && $payoutOptionId != "") {
            $formFields = Gpf_Db_Table_FormFields::getInstance();
            $formName = self::PAYOUT_OPTION . $payoutOptionId;
            $payoutOptionFields = $formFields->getFieldsNoRpc($formName);

            foreach ($payoutOptionFields as $field) {
                try {
                    $payoutOptionUserValue = new Pap_Db_UserPayoutOption();
                    $payoutOptionUserValue->setUserId($row->getId());
                    $payoutOptionUserValue->setFormFieldId($field->get("id"));
                    $payoutOptionUserValue->setValue($form->getFieldValue($field->get("code")));
                    $payoutOptionUserValue->save();
                } catch (Exception $e) {
                }
            }
        }

        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName('quickLaunchSetting');
        $attribute->set(Gpf_Db_Table_UserAttributes::VALUE, 'showDesktop');
        $attribute->setAccountUserId($row->getAccountUserId());
        $attribute->save();

        self::setAffiliateLanguage($form, $row->getAccountUserId());
    }

    public static function setAffiliateLanguage(Gpf_Rpc_Form $form, $accountUserId) {
        if (!$form->existsField('lang')) {
            return;
        }

        if (!Gpf_Lang_Dictionary::isLanguageSupported($form->getFieldValue('lang'))) {
            return;
        }

        $attribute = new Gpf_Db_UserAttribute();
        $attribute->setName(Gpf_Auth_User::LANGUAGE_ATTRIBUTE_NAME);
        $attribute->set(Gpf_Db_Table_UserAttributes::VALUE, $form->getFieldValue('lang'));
        $attribute->setAccountUserId($accountUserId);
        $attribute->save();
    }

    protected function checkBeforeSave($row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
        $result = parent::checkBeforeSave($row, $form, $operationType);

        $context = new Pap_Signup_SignupFormContext(Gpf_Http::getRemoteIp(), $form, $row);
        $fraudProtectionObj = new Pap_Signup_FraudProtection();
        $fraudProtectionObj->check($context);

        return $result && $context->isSaveAllowed();
    }

    protected function sendEmails(Pap_Contexts_Signup $context) {
        $signupEmail = new Pap_Signup_SendEmailToUser();
        $signupEmail->process($context);

        $notificationEmails = new Pap_Signup_SendNotificationEmails();
        $notificationEmails->process($context);
    }

}
?>
