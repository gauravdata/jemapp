<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak, Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: ApplicationSettings.class.php 33893 2011-07-25 12:04:55Z iivanco $
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
class Pap_ApplicationSettings extends Gpf_ApplicationSettings {

    //TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (find out if plugin is active)
    protected function isPluginActive($codename) {
        if(Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($codename)) {
            return "true";
        }
        return "false";
    }

    protected function loadSetting() {
        parent::loadSetting();

        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME);

        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME);
        $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME);

    	$this->addSetting(Pap_Settings::SUPPORT_DIRECT_LINKING);
        $this->addSetting(Pap_Settings::MAIN_SITE_URL);

        $this->addSetting(Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME);
        $this->addSetting(Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME);
        $this->addSetting(Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME);

        $this->addSetting(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
        $this->addSetting(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT);
        $this->addSetting(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK);
        $this->addSetting(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK);
        $this->addSetting(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED);
        $this->addSetting(Pap_Settings::SETTING_LINKING_METHOD);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.ApplicationSettings.loadSetting', $this);
        
        $this->addValue(Pap_Settings::PARAM_NAME_USER_ID, Pap_Tracking_Request::getAffiliateClickParamName());
        $this->addValue(Pap_Settings::PARAM_NAME_BANNER_ID, Pap_Tracking_Request::getBannerClickParamName());
        
        $currentTheme = new Gpf_Desktop_Theme();
        $this->addValue("desktopMode", $currentTheme->getDesktopMode());

        //TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (find out if plugin is active)
        $this->addValue("quickBooksPluginActive", $this->isPluginActive('QuickBooks'));


       	try {
       	    $defaultCurrency = Gpf_Db_Currency::getDefaultCurrency();
       	    $this->addValue("currency_symbol", $defaultCurrency->getSymbol());
       	    $this->addValue("currency_precision", $defaultCurrency->getPrecision());
       	    $this->addValue("currency_wheredisplay", $defaultCurrency->getWhereDisplay());
       	} catch(Gpf_Exception $e) {
       	    $this->addValue("currency_symbol", "Unknown");
       	    $this->addValue("currency_precision", 2);
       	    $this->addValue("currency_wheredisplay", 1);
       	}
    }    
}
?>
