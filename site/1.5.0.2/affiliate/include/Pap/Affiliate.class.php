<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 34225 2011-08-16 09:37:55Z mkendera $
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

class Pap_Affiliate extends Gpf_Module {
    
    public function __construct() {
        parent::__construct('com.qualityunit.pap.AffiliateApplication', 'affiliates', Pap_Application::ROLETYPE_AFFILIATE);
    }
     
    protected function getTitle() {
        return $this->_("%s - Affiliate", Gpf_Application::getInstance()->getName());
    }
    
    protected function onStart() {
        parent::onStart();
        Gpf_Paths::getInstance()->saveServerUrlSettings();
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
        $this->renderPermissionsRequest();
        $this->renderIconSetRequest();
        $this->renderAffiliateScreensRequest();
        $this->renderApplicationSettingsRequest();
        $this->renderAffiliatePanelRequest();
        $this->renderWallpaperRequest();
    }

    protected function getMainDocumentTemplate() {
    	return 'main_aff_html_doc.stpl';
    }

    protected function renderIconSetRequest() {
        $iconSet = new Pap_Common_IconSet();
        Gpf_Rpc_CachedResponse::add($iconSet->getAllIconsNoRpc(), 'Pap_Common_IconSet', 'getAllIcons');
    }

    protected function renderWallpaperRequest() {
        $wallpaperRequest = new Gpf_Desktop_WallPaper();
        Gpf_Rpc_CachedResponse::add($wallpaperRequest->loadNoRpc(), 'Gpf_Desktop_WallPaper', 'load');
    }
    
    protected function renderAffiliateScreensRequest() {
        Gpf_Rpc_CachedResponse::add(Pap_Db_Table_AffiliateScreens::getInstance()->getAllNoRpc(),
        'Pap_Db_Table_AffiliateScreens', 'getAll');
    }

    protected function renderApplicationSettingsRequest() {
        $settings = new Pap_Affiliates_ApplicationSettings();
        Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(),
        'Pap_Affiliates_ApplicationSettings', 'getSettings');
    }
    
    protected function renderAffiliatePanelRequest() {
        $panel = new Pap_Merchants_Config_AffiliatePanel();
        Gpf_Rpc_CachedResponse::add($panel->loadTreeNoRpc(),
        'Pap_Merchants_Config_AffiliatePanel', 'loadTree');
    }
       
    protected function getCachedTemplateNames() {
        return array_merge(parent::getCachedTemplateNames(),
                           array('main', 'breadcrumbs', 'breadcrumbs_entry',
                                 'home', 'period_stats', 'icon_object', 'affiliate_tutorial_video'));
    }
    
    public function assignModuleAttributes(Gpf_Templates_Template $template) {
        parent::assignModuleAttributes($template);
        Pap_Module::assignTemplateVariables($template);
        if (Gpf_Session::getAuthUser()->isLogged()) {
            $template->assignAttributes(Gpf_Session::getAuthUser()->getUserData());
        }
        $template->assign(Pap_Settings::PROGRAM_NAME, $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME)));
        $template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
        Gpf_Plugins_Engine::extensionPoint('Pap_Affiliate.assignTemplateVariables', $template);
    }
    
    public function getDefaultTheme() {
        $this->initDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME));
        return parent::getDefaultTheme();
    }
}
?>
