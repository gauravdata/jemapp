<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: LoginModule.class.php 26591 2009-12-16 12:01:23Z mbebjak $
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

class Gpf_LoginModule extends Gpf_ModuleBase {
    private $loginResponse;
    protected $panelUrl = 'index.php';

    protected function tryToLogin() {
        if (!$this->isAuthUserLogged()) {
            $this->authenticate();
        }

        if ($this->isAuthUserLogged()) {
            $this->redirectToPanel();
        }
    }
    
    protected function redirectToPanel() {
    	Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, $this->panelUrl);
        exit;
    }

    protected function initJsResources() {
    }

    protected function onStart() {
        parent::onStart();
        if ($this->isPostRequest()) {
            $this->loginResponse = $this->processPostRequest();
        }
        $this->tryToLogin();
    }

    protected function initCachedData() {
        parent::initCachedData();
        $this->renderLoginRequest();
        $this->renderLanguagesRequest();
        $this->renderLoginFormLoadRequest();
    }

    protected function renderLoginRequest() {
        if ($this->loginResponse != null) {
            Gpf_Rpc_CachedResponse::add($this->loginResponse, "Gpf_Auth_Service", "authenticate", "loginRequest");
        }
    }

    protected function renderLoginFormLoadRequest() {
        $service = new Gpf_Auth_Service();
        Gpf_Rpc_CachedResponse::add($service->loadNoRpc(), "Gpf_Auth_Service", "load");
    }

    protected function renderLanguagesRequest() {
        $languages = Gpf_Lang_Languages::getInstance();
        Gpf_Rpc_CachedResponse::add($languages->getActiveLanguagesNoRpc(), "Gpf_Lang_Languages", "getActiveLanguages");
    }

    protected function executePostRequest(Gpf_Rpc_Params $params) {
        $authService = new Gpf_Auth_Service();
        return $authService->authenticate($params);
    }

    protected function getCachedTemplateNames() {
        return array_merge(parent::getCachedTemplateNames(), array('login_main', 'window', 'window_left', 'window_header',
        'window_bottom_left', 'window_empty_content','icon_button','login_form',
        'tooltip_popup','form_field', 'button', 'link_button','listbox',
        'listbox_popup','grid_pager'));
    }
}
?>
