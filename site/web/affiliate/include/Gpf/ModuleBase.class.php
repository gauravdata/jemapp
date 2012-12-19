<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani, Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ModuleBase.class.php 32929 2011-05-31 09:15:30Z iivanco $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */

abstract class Gpf_ModuleBase extends Gpf_Object {
    const ERROR_URL = 'errorUrl';
    const SUCCESS_URL = 'successUrl';

    const LICENSE = 'license';
    const SESSION = 'session';
    const PLUGINS = 'activePlugins';

    protected $styleSheets = array();
    protected $gwtModuleName;
    protected $roleType;
    protected $panelName;
    protected $body;
    private $defaultTheme = '';

    public function __construct($gwtModuleName, $panelName, $roleType = '') {
        $this->gwtModuleName = $gwtModuleName;
        $this->panelName = $panelName;
        $this->roleType = $roleType;
    }

    /**
     * @return name of the panel (name of directory in which templates for this panel are located
     */
    public function getPanelName() {
        return $this->panelName;
    }

    public function getRoleType() {
        return $this->roleType;
    }

    protected function getRequestParam($name) {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        return null;
    }

    protected function setError(Exception $e) {
        $this->gwtModuleName = '';
        try {
            $template = new Gpf_Templates_Template('start_error.tpl');
            $template->assign('errorMessage', $e->getMessage());
            $this->body = $template->getHTML();
        } catch(Gpf_ResourceNotFoundException $e){
            Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, Gpf_Paths::getInstance()->getInstallDirectoryPath());
        } catch (Exception $outerException) {
            die(sprintf("Fatal startup error: %s (%s)", $e->getMessage(), $outerException->getMessage()));
        }
    }

    protected function addJsResource($name) {
        Gpf_Contexts_Module::getContextInstance()->addJsResource(
            Gpf_Paths::getInstance()->getResourceUrl($name));
    }
    
    protected function initJsResources() {
        Gpf_Plugins_Engine::extensionPoint('Core.initJsResources', Gpf_Contexts_Module::getContextInstance());
    }

    protected function initStyleSheets() {
        $this->styleSheets = array();
        $this->addStyleSheet("gpf.css");
    }

    protected function initData() {
        Gpf_Rpc_CachedResponse::reset();
        $this->initCachedData();
        $this->addSessionInfoToCache();
        $this->addLicenseToCache();
        $this->addPlugins();
    }

    
    /**
     * @return boolean
     */
    protected function isAuthUserLogged() {
    	return Gpf_Session::getAuthUser()->isLogged() && Gpf_Session::getAuthUser()->isExists();
    }
    
    private function addLicenseToCache() {
        try {
            $license = Gpf_Settings::get(Gpf_Settings_Gpf::LICENSE);
        } catch (Exception $e) {
            return;
        }
        $data = new Gpf_Rpc_Data();
        $data->setValue(self::LICENSE, $license);
        Gpf_Rpc_CachedResponse::addById($data, self::LICENSE);
    }

    private function addPlugins() {
        $plugins = new Gpf_Data_IndexedRecordSet('name');
        $plugins->setHeader(array('name'));
        foreach (Gpf_Plugins_Engine::getInstance()->getConfiguration()->getActivePlugins() as $pluginCode) {
            $plugins->add(array($pluginCode));
        }
        Gpf_Rpc_CachedResponse::addById($plugins, self::PLUGINS);
    }

    private function addSessionInfoToCache() {
        $sessionInfo = new Gpf_Rpc_Data();
        $sessionInfo->setValue(Gpf_Rpc_Params::SESSION_ID, Gpf_Session::getInstance()->getId());
        $sessionInfo->setValue('loggedUser', Gpf_Session::getAuthUser()->getFirstName()
        . ' ' . Gpf_Session::getAuthUser()->getLastName());
        $sessionInfo->setValue('loggedUserEmail', Gpf_Session::getAuthUser()->getUsername());
        $sessionInfo->setValue('serverTime', Gpf_DbEngine_Database::getDateString());
        $sessionInfo->setValue('baseUrl', rtrim(Gpf_Paths::getInstance()->getBaseServerUrl(), '/'));
        $this->setSessionInfo($sessionInfo);
        Gpf_Rpc_CachedResponse::addById($sessionInfo, self::SESSION);
    }

    protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
    }

    protected function initCachedData() {
        $this->renderDictionaryRequest();
        $this->renderTemplatesRequest();
        Gpf_Plugins_Engine::extensionPoint("GpfModuleBase.initCachedData", $this);
    }
    
    protected function renderPermissionsRequest() {
        Gpf_Rpc_CachedResponse::addById(Gpf_Session::getAuthUser()->getPrivileges(), 'permissions');
    }

    protected function renderDictionaryRequest() {
        $dictionary = Gpf_Lang_Dictionary::getInstance();
        Gpf_Rpc_CachedResponse::addEncodedById($dictionary->getEncodedClientMessages(), 'langDictionary');
    }

    protected function renderTemplatesRequest() {
        $templates = new Gpf_Templates_Templates();
        $templates->addToCache($this->getCachedTemplateNames());
    }

    protected function getCachedTemplateNames() {
        return array('notification_window', 'desktop','taskbar','start_button','item_panel',
        'context_menu','quick_launch','sidebar_closed','sidebar','icon_button',
        'main_menu', 'menu_entry', 'menu_section','sub_menu_section',
        'desktop_main_menu','system_menu','desktop_gadget','quick_stats_actions_panel',
        'popup_actions_panel','link_button','gadget_window','gadget_window_topleft',
        'gadget_window_left','gadget_window_bottomleft','gadget_header',
        'item', 'gadget_footer','listbox','listbox_popup','grid_pager',
        'window','window_left','window_header','window_header_refresh','window_bottom_left',
        'window_empty_content','task','page_header','tab_panel','tab_item',
        'grid','grid_nodata','grid_topbuttons','grid_bottombuttons','button',
        'grid_gridline','grid_selector','wallpaper_entry','loading_screen',
        'theme_panel', 'window_move_panel', 'single_content_panel',
        'expired_session_dialog');
    }

    protected function addStyleSheets(array $styleSheets) {
        foreach ($styleSheets as $styleSheet) {
            $this->addStyleSheet($styleSheet);
        }
    }

    protected function addStyleSheet($styleSheet) {
        $defaultStyle = $this->setStyleSheetPath($styleSheet);
        $this->setStyleSheetPath($styleSheet, false, $defaultStyle);
        
    }
    
    private function setStyleSheetPath($styleSheet, $default = true, $defaultTheme = '') {
        try {
            $themeStyleSheet = Gpf_Paths::getInstance()->getStyleSheetUrl($styleSheet, $default);
            if ($default) {
            	$this->styleSheets[$styleSheet . '_default'] = $themeStyleSheet;
            } elseif ($defaultTheme != $themeStyleSheet) {
                $this->styleSheets[$styleSheet] = $themeStyleSheet;
            }
            return $themeStyleSheet;
        } catch (Exception $e) {
        	return '';
        }
    }

    private static function isAcceptGzipEncoding() {
        if (defined('GZIP_ENCODING_DISABLED')) {
            return false;
        }
        if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $encodings = explode(',', strtolower(preg_replace("/s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));
            return in_array('gzip', $encodings);
        }
        return false;
    }

    private static function isAcceptGzip() {
        return false;
        //return self::isAcceptGzipEncoding() && extension_loaded('zlib');
    }

    public static function startGzip() {
        if(self::isAcceptGzip()) {
            ob_start('ob_gzhandler');
        }
    }
    public static function flushGzip() {
        if(self::isAcceptGzip()) {
            ob_end_flush();
        }
    }

    protected function getModuleUrl() {
        if (!strlen($this->gwtModuleName)) {
            return false;
        }
        
        if(self::isAcceptGzipEncoding()) {
            try {
                return Gpf_Paths::getInstance()->getGwtModuleUrl($this->gwtModuleName, true);
            } catch (Gpf_Exception $e) {
            }
        }
        return Gpf_Paths::getInstance()->getGwtModuleUrl($this->gwtModuleName);
    }

    protected function getTitle() {
        return $this->_("Application Title");
    }
    
    protected function getMetaDescription() {
        return $this->_('Application meta description');
    }

    protected function getMetaKeywords() {
        return $this->_('GwtPHP Framework Application, Gwt, PHP');
    }
    
    
    protected function onStart() {
        $this->initSession();
    }

    protected function initSession() {
        $sessionId = null;
        if (array_key_exists('S', $_REQUEST) && $_REQUEST['S'] != '') {
            $sessionId = $_REQUEST['S'];
        }
        Gpf_Session::create($this, $sessionId);
        $this->checkApplication();
    }

    protected function checkApplication() {
        if(Gpf_Application::getInstance()->isInMaintenanceMode()) {
            if(Gpf_Application::getInstance()->isInstalled()) {
                throw new Gpf_Exception($this->_('Maintenance Mode: Update in progress. Please run installer to finish update.'));
            } else {
                throw new Gpf_Exception($this->_('Maintenance Mode: Install in progress'));
            }
            
        }
    }

    protected function getFaviconUrl() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_FAVICON) != '') {
            return Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_FAVICON);
        } else {
            return Gpf_Paths::getInstance()->getImageUrl('favicon.ico');
        }
    }

    public function startAndGet() {
        return $this->getContent();
    }

    /**
     * Return body template
     *
     * @return Gpf_Templates_Template
     */
    protected function getBodyTemplate() {
        return new Gpf_Templates_Template('module_body.stpl');
    }
    
    final private function getContent() {
        $gwtModule = '';
        try {
            $this->onStart();
            $this->body = $this->getBodyTemplate()->getHTML();
            $this->initData();
            $this->initStyleSheets();
            $this->initJsResources();
            $gwtModule = $this->getModuleUrl();
        } catch (Exception $e) {
             $this->setError($e);
        }

        $template = new Gpf_Templates_Template($this->getMainDocumentTemplate());
        $this->assignTemplateVariables($template);
        $template->assign('gwtModuleName', $gwtModule);

        return $template->getHTML();
    }

    final public function start() {
        $this->checkRequirements();
        $content = $this->getContent();
        self::startGzip();
        echo $content;
        self::flushGzip();
    }
    
    private function checkRequirements() {
        // smarty requirement
        if (ini_get('magic_quotes_runtime') == '1' || ini_get('magic_quotes_runtime') == 'On') {
            die('magic_quotes_runtime has to be turned of in php.ini');
        }
        if(!class_exists('ReflectionClass', false)) {
            die('ReflectionClass not found. Please compile PHP with reflection enabled.');
        }
    }
    
    public function loadStaticPage($pageTemplate, $mainDocumentTemplate = '') {
        if($mainDocumentTemplate == '') {
            $mainDocumentTemplate = $this->getMainDocumentTemplate();
        }
         
        $content = $this->getStaticContent($pageTemplate, $mainDocumentTemplate);
        self::startGzip();
        echo $content;
        self::flushGzip();
    }

    protected function getMainDocumentTemplate() {
        return 'main_html_doc.tpl';
    }

    private function getStaticContent($pageTemplate, $mainDocumentTemplate) {
        try {
            $this->onStart();
            $this->initStyleSheets();
        } catch (Exception $e) {
            $this->setError($e);
        }

        $template = new Gpf_Templates_Template($mainDocumentTemplate);
        $this->assignTemplateVariables($template);
        $template->assign('staticPage', $this->getStaticPage($pageTemplate));

        return $template->getHTML();
    }

    protected function getStaticPage($pageTemplate) {
        if($pageTemplate == '') {
            return '';
        }
         
        $template = new Gpf_Templates_Template($pageTemplate);
        $this->assignTemplateVariables($template);
        return $template->getHTML();
    }

    /**
     * Get array of included css files
     *
     * @return unknown
     */
    public function getStyleSheets() {
        return $this->styleSheets;
    }

    protected function assignTemplateVariables(Gpf_Templates_Template $template) {
        $template->assign('title', $this->getTitle());
        $template->assign('metaDescription', $this->getMetaDescription());
        $template->assign('metaKeywords', $this->getMetaKeywords());
        $template->assign('cachedData', Gpf_Rpc_CachedResponse::render());
        $template->assign('stylesheets', $this->styleSheets);
        $template->assign('jsResources', Gpf_Contexts_Module::getContextInstance()->getJsResources());
        $template->assign('jsScripts', Gpf_Contexts_Module::getContextInstance()->getJsScripts());
        $template->assign('body', $this->body);
        $template->assign('faviconUrl', $this->getFaviconUrl());
    }

    protected function authenticate() {
        $loginRequest = new Gpf_Auth_Service();
        $loginRequest->authenticateNoRpc();
        $loginRequest->registerLogin();
        Gpf_Session::refreshAuthUser();
    }

    public function assignModuleAttributes(Gpf_Templates_Template $template) {
        $template->assign('qualityUnitBaseLink', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK));
        $template->assign('qualityUnit', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT));
        $template->assign('qualityUnitCompanyLink', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_COMPANY_LINK));
        $template->assign('qualityUnitPrivacyPolicyLink', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_PRIVACY_POLICY_LINK));
        $template->assign('qualityUnitContactUsLink', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_CONTACT_US_LINK));
        $template->assign('supportEmail', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_SUPPORT_EMAIL));
        $template->assign('qualityUnitAddonsLink', Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_QUALITYUNIT_ADDONS_LINK));
        Gpf_Plugins_Engine::extensionPoint('Core.assignModuleAttributes', $template);
    }

    /*************************************************************/
    /***************** Post request processing *******************/
    /*************************************************************/

    protected function isPostRequest() {
        return (is_array($_POST) && count($_POST) > 0);
    }

    protected function processPostRequest() {
        $this->debug("Processing form by POST called");
        $this->debug($this->convertPostValuesToString());

        $handler = new Gpf_Rpc_FormHandler();
        $params = new Gpf_Rpc_Params($handler->decode($_POST));

        try {
            $response = $this->executePostRequest($params);
        } catch (Gpf_Exception $e) {
            return null;
        }

        $url = '';
        if ($response->isSuccessful() && $this->getPostVariable(self::SUCCESS_URL) != "") {
            $url = $this->getPostVariable(self::SUCCESS_URL);
        } else if($this->getPostVariable(self::ERROR_URL) != "") {
            $url = $this->getPostVariable(self::ERROR_URL);
        }

        if($url != '') {
            echo $this->postResponseTo($url, $response);
            $this->debug("Finished signup form, posting response to URL: $url");
            exit;
        }

        $this->debug("Finished signup form processing, displaying after signup page");
        return $response;
    }

    /**
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    protected function executePostRequest(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception("executePostRequest is not implemented in this module");
    }

    private function convertPostValuesToString() {
        $txt = 'Post parameters: ';
        foreach($_POST as $k =>$v) {
            $txt .= "$k = $v,";
        }

        return $txt;
    }

    private function getPostVariable($name) {
        if (!array_key_exists($name, $_POST)) {
            return "";
        }
        return $_POST[$name];
    }

    private function postResponseTo($url, $response) {
        $template = new Gpf_Templates_Template("post_response.stpl");
        $cumulativeErrorMessage = "";
        $fields = array();
        foreach ($response as $field) {
            $error = $field->get(Gpf_Rpc_Form::FIELD_ERROR);
            $fields[] = array("name" => $field->get(Gpf_Rpc_Form::FIELD_NAME),
                              "value" => $field->get(Gpf_Rpc_Form::FIELD_VALUE),
                              "error" => $error);
            if ($error != "") {
                $cumulativeErrorMessage .= $error . "<br>";
            }
        }
        if ($response->getErrorMessage() != '') {
            $cumulativeErrorMessage .= $response->getErrorMessage() . "<br>";
        }
        $template->assign('fields', $fields);
        $template->assign('errorMessage', $response->getErrorMessage());
        $template->assign('cumulativeErrorMessage', $cumulativeErrorMessage);
        $template->assign('successMessage', $response->getInfoMessage());
        $template->assign('url', $url);
        return $template->getHTML();
    }

    protected function debug($msg) {
        Gpf_Log::debug($msg);
    }

    protected function initDefaultTheme($theme) {
        $this->defaultTheme = $theme;
    }

    public function isThemeValid($theme) {
        if(strpos($theme, '_') === 0) {
            return false;
        }
        $themePath = Gpf_Paths::getInstance()->getTopTemplatePath() .
        $this->getPanelName() . '/' .
        $theme . '/';
        if (!Gpf_Io_File::isFileExists($themePath)
        || !Gpf_Io_File::isFileExists($themePath . Gpf_Desktop_Theme::CONFIG_FILE_NAME)) {
            return false;
        }
        
        $themeObj = new Gpf_Desktop_Theme($theme, $this->getPanelName());
        return $themeObj->isEnabled();
    }

    public function getDefaultTheme() {
        if($this->isThemeValid($this->defaultTheme)) {
            return $this->defaultTheme;
        }
        return $this->getFirstAvailableTheme();
    }

    private function getFirstAvailableTheme() {
        $themeManager = new Gpf_Desktop_ThemeManager();
        return $themeManager->getFirstTheme($this->panelName);
    }

    /**
     * Overwrite this function in specific module.
     * Function should return default demo username.
     *
     * @return string
     */
    public function getDemoUsername() {
        return '';
    }

    /**
     * Overwrite this function in specific module.
     * Function should return default demo password.
     *
     * @return string
     */
    public function getDemoPassword() {
        return '';
    }
}
?>
