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
class Pap_Merchants_ApplicationSettings extends Pap_ApplicationSettings {
    
    protected function loadSetting() {
        parent::loadSetting();        

       	$this->addValue("merchantName", Gpf_Session::getAuthUser()->getFirstName().' '.Gpf_Session::getAuthUser()->getLastName());
       	
       	$this->addSetting(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME);
       	$this->addSetting(Pap_Settings::DEFAULT_AFFILIATE_SIGNUP_THEME);
       	$this->addSetting(Pap_Settings::DEFAULT_MERCHANT_PANEL_THEME);
       	
       	$this->addSetting(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME);
       	$this->addSetting(Pap_Settings::GETTING_STARTED_SHOW);
       	$this->addSetting(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
       	$this->addValue("variationName", Gpf_Settings::get(Gpf_Settings_Gpf::VARIATION));
       	$this->addValue('accountid', Gpf_Session::getAuthUser()->getAccountId());
    }   
}
?>
