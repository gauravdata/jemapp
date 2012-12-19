<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Merchant.class.php 34225 2011-08-16 09:37:55Z mkendera $
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

class Pap_Merchant extends Gpf_Module {
    /**
     * @var Gpf_Desktop_WindowManager
     */
    private $windowManager;
    const MERCHANT_PANEL_NAME = 'merchants';
    
    public function __construct() {
        parent::__construct('com.qualityunit.pap.MerchantApplication', self::MERCHANT_PANEL_NAME, Pap_Application::ROLETYPE_MERCHANT);
        $this->windowManager = new Gpf_Desktop_WindowManager();
    }

    protected function getTitle() {
        return Gpf_Application::getInstance()->getName() . ' - ' . $this->_('Merchant');
    }

    protected function onStart() {
        parent::onStart();
        Gpf_Paths::getInstance()->saveServerUrlSettings();
        Gpf_Plugins_Engine::getInstance()->getAvailablePlugins();
    }

    protected function initCachedData() {
    	parent::initCachedData();
        $this->renderIconSetRequest();
        $this->renderMerchantSettingsRequest();
        $this->renderPermissionsRequest();

        $this->renderMenuRequest();
        $this->renderWindowManagerRequest();
        $this->renderQuickLaunchRequest();
        $this->renderGadgetRequest();
        $this->renderSideBarRequest();
        $this->renderWallpaperRequest();
    }

    protected function initStyleSheets() {
        parent::initStyleSheets();
        $this->addStyleSheets(Pap_Module::getStyleSheets());
    }

    protected function renderMenuRequest() {
        $menuRequest = new Pap_Merchants_Menu();
        Gpf_Rpc_CachedResponse::add($menuRequest->getNoRpc(), 'Pap_Merchants_Menu', 'get');
    }

    protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
        Pap_Module::setSessionInfo($sessionInfo);
    }

    protected function renderWallpaperRequest() {
        $wallpaperRequest = new Gpf_Desktop_WallPaper();
        Gpf_Rpc_CachedResponse::add($wallpaperRequest->loadSelectedWallpaperNoRpc(), 'Gpf_Desktop_WallPaper', 'loadSelectedWallpaper');
    }

    protected function renderIconSetRequest() {
        $iconSet = new Pap_Common_IconSet();
        Gpf_Rpc_CachedResponse::add($iconSet->getAllIconsNoRpc(), 'Pap_Common_IconSet', 'getAllIcons');
    }

    protected function renderMerchantSettingsRequest() {
        $settings = new Pap_Merchants_ApplicationSettings();
        Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(),
            'Pap_Merchants_ApplicationSettings', 'getSettings');
    }

    protected function renderWindowManagerRequest() {
        Gpf_Rpc_CachedResponse::add($this->windowManager->getWindowsNoRpc(),
            'Gpf_Desktop_WindowManager', 'getWindows');
    }

    protected function renderQuickLaunchRequest() {
        Gpf_Rpc_CachedResponse::add($this->windowManager->getQuickLaunchNoRpc(),
            'Gpf_Desktop_WindowManager', 'getQuickLaunch');
    }

    protected function renderGadgetRequest() {
        $gadgetManager = new Gpf_GadgetManager();
        Gpf_Rpc_CachedResponse::add($gadgetManager->getGadgetsNoRpc(),
            'Gpf_GadgetManager', 'getGadgets');
    }

    protected function renderSideBarRequest() {
        Gpf_Rpc_CachedResponse::add($this->windowManager->loadSideBarNoRpc(),
            'Gpf_Desktop_WindowManager', 'loadSideBar');
    }

    public function getDefaultTheme() {
        $this->initDefaultTheme(Pap_Branding::DEFAULT_MERCHANT_PANEL_THEME);
        return parent::getDefaultTheme();
    }

    protected function getCachedTemplateNames() {
        return array_merge(parent::getCachedTemplateNames(),
                           array('main', 'breadcrumbs', 'merchant_tutorial_video'));
    }

    public function assignModuleAttributes(Gpf_Templates_Template $template) {
        parent::assignModuleAttributes($template);
        Pap_Module::assignTemplateVariables($template);
        $template->assign(Pap_Settings::PROGRAM_NAME, $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME)));
        $template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
        if (Gpf_Session::getAuthUser()->isLogged()) {
            $template->assignAttributes(Gpf_Session::getAuthUser()->getUserData());
        }
    }

}
?>
