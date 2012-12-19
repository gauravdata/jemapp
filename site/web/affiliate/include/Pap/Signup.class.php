<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Signup.class.php 32066 2011-04-13 07:11:23Z iivanco $
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

class Pap_Signup extends Pap_SignupBase {

    public function __construct() {
        parent::__construct('com.qualityunit.pap.SignupApplication', 'signup', 'A');
    }

    protected function getFormId() {
        return 'affiliateForm';
    }

    protected function getSignupFormService() {
        return 'Pap_Signup_AffiliateForm';
    }

    protected function getSignupSettingsClassName() {
        return 'Pap_Common_SignupApplicationSettings';
    }

    protected function getSignupTemplateName() {
        return 'signup_fields';
    }

    protected function getMainDocumentTemplate() {
        return 'main_signup_html_doc.stpl';
    }

    protected function getCachedTemplateNames() {
        return array_merge(parent::getCachedTemplateNames(), array('post_signup_page'));
    }

    protected function executePostRequest(Gpf_Rpc_Params $params) {
        $signupFormHandler = new Pap_Signup_AffiliateForm();
        return $signupFormHandler->add($params);
    }

    public function getDefaultTheme() {
        $this->initDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SIGNUP_THEME));
        return parent::getDefaultTheme();
    }

    /**
     * Checks if signup form is ok
     *
     * @service affiliate_signup_setting read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function checkSignupFieldsRpc(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            $this->checkSignupFields();
            $action->addOk();
        } catch (Exception $e) {
            $action->setErrorMessage($e->getMessage());
            $action->addError();
        }
        return $action;
    }
}
?>
