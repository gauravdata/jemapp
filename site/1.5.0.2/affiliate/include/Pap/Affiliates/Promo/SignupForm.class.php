<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */


class Pap_Affiliates_Promo_SignupFormPanel extends Gpf_Ui_DynamicFormPanel {
    function __construct() {
        parent::__construct("signup_fields.tpl", "affiliateForm", "signup");
        $this->addStaticField("username", $this->_("Username (Email)"), "T", "M");
        $this->addStaticField("firstname", $this->_("First name"), "T", "M");
        $this->addStaticField("lastname", $this->_("Last name"), "T", "M");
        
        $this->addStaticField("agreeWithTerms", $this->_("I agree with terms & conditions"), "B", "O");
        
        $terms = Gpf_Lang::_localizeRuntime(Gpf_Settings::get(Pap_Settings::SIGNUP_TERMS_SETTING_NAME));
        $this->add("<label>" . $this->_('Terms & conditions') . "</label><textarea>" . $terms . "</textarea>",  'termsAndConditions');
        
    }
}

/**
 * @package PostAffiliatePro
 */
class Pap_Affiliates_Promo_SignupForm extends Gpf_Object  {

    public static function getSignupScriptUrl($useParent = true, Pap_Common_User $user = null) {  	
        $url = Gpf_Paths::getAffiliateSignupUrl();
        if ($useParent) {
            if ($user == null) {
            	$user = Pap_Affiliates_User::getUserById(Gpf_Session::getAuthUser()->getPapUserId());
            }
            $url .= '?' . Pap_Tracking_Request::getAffiliateClickParamName() . '=' . $user->getRefId();
        }
        return $url;   
    }

    /**
     * @service affiliate_signup_form read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function loadMerchant(Gpf_Rpc_Params $params) {
        return $this->loadNoRpc($params, false);
    }
    
    /**
     * @service affiliate_signup_form read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function load(Gpf_Rpc_Params $params) {
        return $this->loadNoRpc($params, true);
    }
    
    public function loadNoRpc(Gpf_Rpc_Params $params, $useParent = true) {
        $data = new Gpf_Rpc_Data($params);
        $signupFormPanel = new Pap_Affiliates_Promo_SignupFormPanel();
        $addScript = '';
        if ($useParent) {       
            $parentUserId = '<input type="hidden" name="parentuserid"'  
                            . ' value="' . Gpf_Session::getAuthUser()->getPapUserId() . '" />';
        } else {
            $parentUserId = '<input type="hidden" name="parentuserid" id="parentuserid" value="" />';
            $scriptUrl = Gpf_Paths::getInstance()->getFullScriptsUrl().'salejs.php';
            $addScript = '<script id="pap_x2s6df8d" 
                                  src="'.$scriptUrl.'" type="text/javascript">
                          </script>
                          <script type="text/javascript">
                            PostAffTracker.writeAffiliateToCustomField(\'parentuserid\');
                          </script>';
        }
        if ($signupFormPanel->containsId("parentuserid")) {
            $signupFormPanel->add($parentUserId, "parentuserid");
        } else {
            $signupFormPanel->addWidget($parentUserId, "parentuserid");
        }
        $signupFormPanelHtml = '<form action="' . self::getSignupScriptUrl($useParent) . '" method="post">' 
                               . $signupFormPanel->render()
                               . '</form>'
                               . $addScript;
        
        $data->setValue("formSource", $signupFormPanelHtml);
        $data->setValue("formPreview", $signupFormPanelHtml);
        
        return $data;
    }
}
?>
