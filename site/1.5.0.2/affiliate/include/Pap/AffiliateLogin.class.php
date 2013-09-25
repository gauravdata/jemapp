<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: AffiliateLogin.class.php 27612 2010-03-23 13:24:47Z mbebjak $
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

class Pap_AffiliateLogin extends Gpf_LoginModule {
    protected $panelUrl = 'panel.php';

    public function __construct() {
        parent::__construct('com.qualityunit.pap.AffiliateLoginModule', 'affiliates', Pap_Application::ROLETYPE_AFFILIATE);
    }

    protected function getMainDocumentTemplate() {
        return 'main_aff_html_doc.stpl';
    }

    protected function getTitle() {
        return Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
    }

    protected function initStyleSheets() {
        parent::initStyleSheets();
        $this->addStyleSheets(Pap_Module::getStyleSheets());
    }

    protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
        Pap_Module::setSessionInfo($sessionInfo);
    }

    protected function initCachedData() {
        parent::initCachedData();
        $this->renderApplicationSettingsRequest();
    }

    protected function getCachedTemplateNames() {
        return array('notification_window', 'icon_button', 'link_button',
        'listbox','listbox_popup', 'button', 'loading_screen', 'window','window_left',
        'window_header','window_bottom_left', 'grid_pager',
        'window_empty_content', 'login_main', 'login_form', 'tooltip_popup', 'form_field', 'select_account_form');
    }

    public function getDefaultTheme() {
        $this->initDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME));
        return parent::getDefaultTheme();

    }

    public function assignModuleAttributes(Gpf_Templates_Template $template) {
        parent::assignModuleAttributes($template);
        Pap_Module::assignTemplateVariables($template);
        $template->assign(Pap_Settings::PROGRAM_NAME, $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME)));
        $template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
    }
     
    protected function renderApplicationSettingsRequest() {
        $settings = new Pap_LoginApplicationSettings();
        Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(), "Pap_LoginApplicationSettings", "getSettings");
    }

    protected function assignTemplateVariables($template) {
        parent::assignTemplateVariables($template);
        Pap_Module::assignTemplateVariables($template);
    }

    /**
     * Overwrite this function in specific module.
     * Function should return default demo username.
     *
     * @return string
     */
    public function getDemoUsername() {
        return Pap_Branding::DEMO_AFFILIATE_USERNAME;
    }

    /**
     * Overwrite this function in specific module.
     * Function should return default demo password.
     *
     * @return string
     */
    public function getDemoPassword() {
        return Pap_Branding::DEMO_PASSWORD;
    }
}
?>
