<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: ApplicationSettings.class.php 16785 2008-03-31 13:20:07Z vzeman $
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
class Pap_Affiliates_ApplicationSettings extends Pap_ApplicationSettings {
    
    protected function loadSetting() {
        parent::loadSetting();        
        
        $this->addSetting(Pap_Settings::AFFILIATE_LOGOUT_URL);
        $this->addSetting(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
        
        $this->addValue('signupSubaffiliatesLink', Pap_Affiliates_Promo_SignupForm::getSignupScriptUrl());
        $this->addValue('signupPageUrl', Gpf_Paths::getAffiliateSignupUrl());       
        
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED);
        $this->addSetting(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON);
        $this->addSetting(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED);
        $this->addSetting(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON);
        $this->addSetting(Pap_Settings::AFFILIATE_CANNOT_CHANGE_HIS_USERNAME);
    }   
}
?>
