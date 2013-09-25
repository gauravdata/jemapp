<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Session.class.php 26583 2009-12-15 17:42:30Z rdohan $
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
class Gpf_Session extends Gpf_Object {
    const AUTH_USER = 'AuthUser';
    const MODULE = 'Module';
    const TIME_OFFSET = 'timeOffset';
    
    private $name;
    protected $started = false;
    /**
     * @var Gpf_Auth_User
     */
    protected $authUser;
    /**
     * @var Gpf_Session
     */
    protected static $instance = null;

    public static function refreshAuthUser() {
        self::$instance->createAuthUser();
    }
    
    /**
     *
     * @return Gpf_Session
     */
    public static function getInstance() {
        if(self::$instance === null) {
            throw new Gpf_Exception('Session not initialized.');
        }
        return self::$instance;
    }
    
    public static function getRoleType() {
        return self::getModule()->getRoleType();
    }
    
    public static function create(Gpf_ModuleBase $module = null, $sessionId = null, $start = true) {
        if($module === null) {
            $module = new Gpf_System_Module();
        }
        if (self::$instance != null) {
            return;
        }
        self::$instance = new Gpf_Session(self::getSessionName($module->getRoleType()));
        if ($sessionId !== null) {
            self::$instance->setId($sessionId);
        }
        if ($start) {
            self::$instance->start();
        }
        self::$instance->setVarRaw(self::MODULE, $module);
        self::$instance->createAuthUser();
    }
    
    /**
     * Load session and compute if session is not expired
     *
     * @param string $sessionId
     */
    public static function load($sessionId) {
        if (self::$instance != null) {
            return;
        }
        self::$instance = new Gpf_Session(self::getSessionName('RPC'));
        if ($sessionId !== null) {
            self::$instance->setId($sessionId);
        }
        self::$instance->start();
        
        if (!self::$instance->existsVar(self::AUTH_USER)) {
            throw new Gpf_Rpc_SessionExpiredException();
        }
        
        self::$instance->createAuthUser();
    }
    
    /**
     * @return Gpf_Auth_User
     */
    public static function getAuthUser() {
        if(self::getInstance()->authUser === null) {
            throw new Gpf_Exception('AuthUser not created yet');
        }
        return self::getInstance()->authUser;
    }
    
    /**
     * @return Gpf_ModuleBase
     */
    public static function getModule() {
        if(self::getInstance()->getVar(self::MODULE) === null) {
            throw new Gpf_Exception('Module not set yet');
        }
        return self::getInstance()->getVar(self::MODULE);
    }
    
    public static function set(Gpf_Session $session) {
        self::$instance = $session;
    }
    
    public function save(Gpf_Auth_User $authUser) {
        $this->setVarRaw(self::AUTH_USER, $authUser);
        $this->authUser = $authUser;
    }
    
    public function getId() {
        return session_id();
    }
    
    public static function getSessionName($panel) {
        return $panel . '_' . Gpf_Application::getInstance()->getCode() . "_sid";
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setVar($var, $value) {
        if($var == self::AUTH_USER || $var == self::MODULE || $var == self::TIME_OFFSET) {
            throw new Gpf_Exception("Reserved session variable");
        }
        $this->setVarRaw($var, $value);
    }
    
    public function getVar($var) {
        if($this->existsVar($var)) {
            return $_SESSION[$var];
        }
        return false;
    }

    public function destroy() {
        $this->authUser = null;
        if($this->isStarted()) {
            session_unset();
            session_destroy();
        }
        $this->started = false;
        if (isset($_COOKIE[$this->name])) {
            setcookie($this->name, '', time()-42000, '/');
        }
    }
    
    protected function __construct($name = null) {
        if($name !== null) {
            $this->name = $name;
        }
    }
    
    protected function setId($id) {
        session_id($id);
    }
    
    public function existsVar($var) {
        return isset($_SESSION) && isset($_SESSION[$var]);
    }
    
    protected function start() {
        if(!$this->isStarted()) {
            if(strlen($this->name)) {
                session_name($this->name);
            }
            @session_start();
            $this->started = true;
        }
    }
    
    protected function createAuthUser() {
        if (!$this->existsVar(self::AUTH_USER)) {
            $authUser = Gpf::newObj(Gpf_Application::getInstance()->getAuthClass());
            $this->authUser = $authUser->createAnonym();
            $this->save($this->authUser);
        } else {
            $this->authUser = $this->getVar(self::AUTH_USER);
        }
        $this->authUser->init();
    }
    
    private function isStarted() {
        return $this->started;
    }
    
    protected function setVarRaw($var, $value) {
        $_SESSION[$var] = $value;
    }

    /**
     * Set time difference between client and server in seconds
     * Offset is computed as clientTime - serverTime
     *      *
     * @param integer $offset Time difference between client and server in seconds
     */
    public function setTimeOffset($offset) {
        $this->setVarRaw(self::TIME_OFFSET, $offset);
    }
    
    /**
     * Get time offset between client and server in seconds.
     * Offset is computed as clientTime - serverTime = offset
     *
     * @return integer time difference between client and server in seconds
     */
    public function getTimeOffset() {
    	if ($this->getVar(self::TIME_OFFSET) !== false) {
    		return $this->getVar(self::TIME_OFFSET);
    	}
        return 0;
    }
}
?>
