<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 20472 2008-09-02 09:29:53Z mbebjak $
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
class Pap_Module extends Gpf_Object {
    
    public static function assignTemplateVariables(Gpf_Templates_Template $template) {
        if (Gpf_Session::getAuthUser()->isLogged()) {
            $template->assign('isLogged', '1');
        } else {
            $template->assign('isLogged', '0');
        }
        $template->assign('papCopyrightText', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT));
    	$template->assign('papVersionText', 'version ' . Gpf_Application::getInstance()->getVersion());
    	$template->assign('postAffiliatePro', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
    	$template->assign('qualityUnitPostaffiliateproLink', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK));
    	$template->assign('postAffiliateProHelp', Gpf_Settings::get(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK));
    	$template->assign('qualityUnitChangeLog', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK));
    	$template->assign('knowledgebaseUrl', Gpf_Settings::get(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK));
    	$template->assign('PAP', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP));
    	$template->assign('tutorialVideosBaseLink', Gpf_Settings::get(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK));
    }
    
    public static function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
        $sessionInfo->setValue('extra1', Pap_Db_Table_Users::getAffiliateCount());
    }
    
    /**
     *
     * @return array
     */
    public static function getStyleSheets() {
        return array('pap4.css', 'custom.css');
    }
}
?>
