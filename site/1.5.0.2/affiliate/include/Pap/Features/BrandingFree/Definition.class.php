<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_BrandingFree_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'BrandingFree';
        $this->name = $this->_('Branding free');
        $this->description = $this->_('Branding Free is a special feature that allows you to change the links in the footer of panels and signup form from "Powered by %s" to your own text and link.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO), Gpf_Application::getKnowledgeHelpUrl('features-and-functions/branding-free.html'));
        Gpf_Application::getKnowledgeHelpUrl('');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.AffiliateGeneralSettingsForm.load', 'Pap_Features_BrandingFree_Config', 'load');
        $this->addImplementation('PostAffiliate.AffiliateGeneralSettingsForm.save', 'Pap_Features_BrandingFree_Config', 'save');
    }
    
    public function onDeactivate() {
        Gpf_Settings::set(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO, 'Post Affiliate Pro');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK, 'http://www.qualityunit.com');
        Gpf_Settings::set(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK, 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro/');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT, 'Quality Unit');
        Gpf_Settings::set(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK, 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro.html');
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK, 'http://www.qualityunit.com/postaffiliatepro/');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK, 'http://www.qualityunit.com/company/');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK, 'http://www.qualityunit.com/company/privacy-policy-quality-unit');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK, 'http://www.qualityunit.com/company/contact-us');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_FAVICON, '');
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK, 'http://bugs.qualityunit.com/mantis/changelog_page.php');
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK, 'http://addons.qualityunit.com');
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_SUPPORT_EMAIL, 'support@qualityunit.com');
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_PAP, 'PAP');
        Gpf_Settings::set(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK, 'http://paphelp.qualityunit.com/pap4/');
        Gpf_Settings::set(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED, Gpf::YES);
    }
}
?>
