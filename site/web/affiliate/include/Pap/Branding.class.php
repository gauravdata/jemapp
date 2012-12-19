<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AuthUser.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Branding {
    const DEFAULT_BRANDING_KNOWLEDGEBASE_LINK = 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro/';
    const DEFAULT_BRANDING_POST_AFFILIATE_PRO_HELP_LINK = 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro.html';
    const DEFAULT_BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK = 'http://www.qualityunit.com/postaffiliatepro/';
    const DEFAULT_BRANDING_QUALITYUNIT_CHANGELOG_LINK = 'http://bugs.qualityunit.com/mantis/changelog_page.php';
    const DEFAULT_BRANDING_QUALITYUNIT_PAP = 'PAP';
    const DEFAULT_BRANDING_TEXT_POST_AFFILIATE_PRO = 'Post Affiliate Pro';
    const DEFAULT_BRANDING_TUTORIAL_VIDEOS_BASE_LINK = 'http://paphelp.qualityunit.com/pap4/';
    const DEFAULT_BRANDING_TEXT = '<a class="papCopyright" href="http://www.qualityunit.com/postaffiliatepro/" target="_blank">Affiliate Software by Post Affiliate Pro</a>';
    
    const DEMO_MERCHANT_USERNAME = 'merchant@example.com';
    const DEMO_AFFILIATE_USERNAME = 'affiliate@example.com';
    const DEMO_PASSWORD = 'demo';
    
    const DEFAULT_MERCHANT_PANEL_THEME = 'blue_aero';
    const DEFAULT_AFFILIATE_PANEL_THEME = 'classic_wide';
    const DEFAULT_SIGNUP_THEME = 'classic';
    
    const DEFAULT_LANGUAGE_CODE = 'en-US';
    
    public static function initDefaultCurrency(Gpf_Db_Currency $currency) {
        $currency->setId('usd00000');
        $currency->setName('USD');
        $currency->setSymbol('$');
        $currency->setPrecision(2);
        $currency->setWhereDisplay(Gpf_Db_Currency::DISPLAY_LEFT);
    }
}
?>
