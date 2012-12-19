<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani, Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ModuleBase.class.php 20018 2008-08-20 15:37:36Z aharsani $
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

class Gpf_System_Session extends Gpf_Session {
    public static function create(Gpf_ModuleBase $module = null, $sessionId = null) {
        if($module === null) {
            $module = new Gpf_System_Module();
        }
        if (self::$instance != null) {
            return;
        }
        self::$instance = new self(self::getSessionName($module->getRoleType()));
        if ($sessionId !== null) {
            self::$instance->setId($sessionId);
        }
        self::$instance->start();
        self::$instance->setVarRaw(self::MODULE, $module);
        self::$instance->createAuthUser();
    }
    
    protected function createAuthUser() {
        if (!$this->existsVar(self::AUTH_USER)) {
            $authUser = Gpf::newObj(Gpf_Application::getInstance()->getAuthClass());
            $this->authUser = $authUser->createAnonym(); 
            $this->save($this->authUser);
        } else {
            $this->authUser = $this->getVar(self::AUTH_USER);
        }
    }
}
?>
