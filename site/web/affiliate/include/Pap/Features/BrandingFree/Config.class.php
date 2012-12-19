<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Features_BrandingFree_Config extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_BrandingFree_Config();
    }

    /**
     * @service branding read
     * @param $fields
     */
    public function load(Gpf_Rpc_Form &$form) {
        $form->setField(Pap_Settings::BRANDING_TEXT,
            Gpf_Settings::get(Pap_Settings::BRANDING_TEXT));
        $form->setField(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO,
            Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
        $form->setField(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_FAVICON,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_FAVICON));
        $form->setField(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK,
            Gpf_Settings::get(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT));
        $form->setField(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK,
            Gpf_Settings::get(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK));
        $form->setField(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK,
            Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_FAVICON,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_FAVICON));
        $form->setField(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK,
            Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK));
        $form->setField(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_SUPPORT_EMAIL,
            Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_SUPPORT_EMAIL));
        $form->setField(Pap_Settings::BRANDING_QUALITYUNIT_PAP,
            Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP));
        $form->setField(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK,
            Gpf_Settings::get(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK));
        $form->setField(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED,
            Gpf_Settings::get(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED));
    }

    /**
     * @service branding write
     * @param $fields
     */
    public function save(Gpf_Rpc_Form &$form) {
        Gpf_Settings::set(Pap_Settings::BRANDING_TEXT, $form->getFieldValue(Pap_Settings::BRANDING_TEXT));
        Gpf_Settings::set(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO, $form->getFieldValue(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_FAVICON, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_FAVICON));
        Gpf_Settings::set(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK, $form->getFieldValue(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT));
        Gpf_Settings::set(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK, $form->getFieldValue(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK));
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK, $form->getFieldValue(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_FAVICON, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_FAVICON));
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK, $form->getFieldValue(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK));
        Gpf_Settings::set(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_SUPPORT_EMAIL, $form->getFieldValue(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_SUPPORT_EMAIL));
        Gpf_Settings::set(Pap_Settings::BRANDING_QUALITYUNIT_PAP, $form->getFieldValue(Pap_Settings::BRANDING_QUALITYUNIT_PAP));
        Gpf_Settings::set(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK, $form->getFieldValue(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK));
        Gpf_Settings::set(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED, $form->getFieldValue(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED));
    }
}

?>
