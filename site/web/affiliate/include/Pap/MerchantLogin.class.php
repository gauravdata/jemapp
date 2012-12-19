<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: MerchantLogin.class.php 27659 2010-03-30 14:32:48Z mkendera $
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

class Pap_MerchantLogin extends Gpf_LoginModule {
    public function __construct() {
        parent::__construct('com.qualityunit.pap.MerchantLoginModule', 'merchants', Pap_Application::ROLETYPE_MERCHANT);
    }

    protected function initStyleSheets() {
        parent::initStyleSheets();
        $this->addStyleSheets(Pap_Module::getStyleSheets());
    }

    protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
        Pap_Module::setSessionInfo($sessionInfo);
    }

    protected function getCachedTemplateNames() {
        return array('notification_window', 'icon_button', 'link_button',
        'listbox','listbox_popup', 'button', 'loading_screen', 'window','window_left',
        'window_header','window_bottom_left', 'grid_pager', 'window_move_panel', 'single_content_panel',
        'window_empty_content', 'login_main', 'login_form', 'tooltip_popup', 'form_field', 'select_account_form');
    }

    public function getDefaultTheme() {
        $this->initDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_MERCHANT_PANEL_THEME));
        return parent::getDefaultTheme();
    }

    protected function getTitle() {
        return Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
    }

    protected function initCachedData() {
        parent::initCachedData();
        $this->renderApplicationSettingsRequest();
    }

    protected function renderApplicationSettingsRequest() {
        $settings = new Pap_LoginApplicationSettings();
        Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(), "Pap_LoginApplicationSettings", "getSettings");
    }

    public function assignModuleAttributes(Gpf_Templates_Template $template) {
        parent::assignModuleAttributes($template);
        Pap_Module::assignTemplateVariables($template);
        $template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
    }

    /**
     * Overwrite this function in specific module.
     * Function should return default demo username.
     *
     * @return string
     */
    public function getDemoUsername() {
        return Pap_Branding::DEMO_MERCHANT_USERNAME;
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
