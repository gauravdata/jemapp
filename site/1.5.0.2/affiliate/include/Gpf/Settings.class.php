<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani, Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Gpf_Settings extends Gpf_Object {
    
     /**
     * @var Gpf_Settings_AccountSettings instances
     */
    protected static $instances = array();
    protected static $instance;
    /**
     * @var Gpf_Settings_Define
     */
    protected $settings;
    
    protected function __construct() {
        $this->settings = Gpf_Application::getInstance()->createSettings();
    }
    
    private function init() {
        $this->settings->init();
    }
    
    /**
     * @return Gpf_Settings
     */
    private static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Gpf_Settings();
            self::$instance->init();
        }
        return self::$instance;
    }
       
    public static function get($name) {
        return self::getInstance()->getSetting($name);
    }
    
    public static function set($name, $value) {
        self::getInstance()->setSetting($name, $value);
    }
    
    /**
     * @param $accountId
     * @return Gpf_Settings_AccountSettings
     */
    public static function getAccountSettings($accountId) {
        if (!array_key_exists($accountId, self::$instances)) {
            self::$instances[$accountId] = new Gpf_Settings_AccountSettings(self::getInstance()->getAccountSettingsInstance($accountId),
                                                                            self::getInstance()->getAccountSettingsInstance());
        }
        return self::$instances[$accountId];
    }
    
    /**
     * @param $accountId
     * @return Gpf_Settings_Base
     */
    protected function getAccountSettingsInstance($accountId = null) {
        return Gpf_Settings_Base::getInstance($this->settings, $accountId);
    }
    
    protected function getSetting($name) {
        return $this->getSettingsInstance($name)->readSetting($name);
    }
    
    protected function setSetting($name, $value) {
        $this->getSettingsInstance($name)->writeSetting($name, $value);
    }
    
    /**
     * @param $name
     * @return Gpf_Settings_Base
     */
    private function getSettingsInstance($name) {
        $this->settings->checkSetting($name);
        return $this->getAccountSettingsInstance($this->getAccountId($name));
    }
    
    private function getAccountId($name) {
        if ($this->settings->isAccountSetting($name)) {
            try {
                return Gpf_Session::getAuthUser()->getAccountId();
            } catch (Gpf_Exception $e) {
            }
            return Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
        }
        return null;
    }
}
?>
