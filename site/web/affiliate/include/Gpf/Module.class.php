<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Module.class.php 26591 2009-12-16 12:01:23Z mbebjak $
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

abstract class Gpf_Module extends Gpf_ModuleBase {
    
    protected function checkIfUserIsLogged() {
        if ($this->isAuthUserLogged()) {
            return;
        }
        $this->login();
    }
    
    protected function onStart() {
        parent::onStart();
        $this->checkIfUserIsLogged();
    }
    
    protected function login() {
        $this->authenticate();
        
        if (!$this->isAuthUserLogged()) {
            $this->redirectToLogin();
        }
    }
    
    protected function redirectToLogin() {
    	Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, 'login.php');
		exit;
    }
}
?>
