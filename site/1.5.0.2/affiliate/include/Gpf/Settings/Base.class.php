<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani, Miso Bebjak
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
 * class that implements the loading and saving of settings from/to file or from DB
 * Instance of this class is created in a Gpf_SettingsBase class
 *
 * @package GwtPhpFramework
 */
class Gpf_Settings_Base extends Gpf_Object {

    const MAX_RETRIES = 50;
    
    /**
     *
     * @var Gpf_File_Settings
     */
    private $file;

    private $accountId = '';

    /**
     * @var Gpf_Settings_Define
     */
    private $defines;

    /**
     * @var Gpf_GlobalSettings instances
     */
    static protected $instances = array();

    /**
     * Settings cache
     *
     * @var array
     */
    protected $cache = array();
    
    /**
     * 
     * @var Gpf_Settings_Driver_Locker
     */
    protected $driver;

    protected function __construct(Gpf_Settings_Define $defines, $accountId) {
        $this->file = new Gpf_File_Settings($accountId);
        $this->accountId = $accountId;
        $this->defines = $defines;
        $this->createLockDriver();
    }
    
    private function createLockDriver() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'SUN') {
            $this->driver = new Gpf_Settings_Driver_LockSunos();
            return;
        }
        $this->driver = new Gpf_Settings_Driver_Lock();
    }

    /**
     * returns instance of Gpf_Settings class
     *
     * @return Gpf_Settings_Base
     */
    public static function getInstance(Gpf_Settings_Define $defines, $accountId) {
        if (!array_key_exists($accountId, self::$instances)) {
            self::$instances[$accountId] = new Gpf_Settings_Base($defines, $accountId);
        }
        return self::$instances[$accountId];
    }

    public function readSetting($name) {
        $this->defines->checkSetting($name);

        if ($this->defines->isFileSetting($name)) {
            $file = $this->lock(LOCK_SH);
            try {
                $this->file->forceReload();
                $this->loadFileSetting($name, $file);
            } catch (Exception $e){
                $this->unlock($file);
                throw $e;
            }
            $this->unlock($file);
            
        } else {
            //Load setting from cache if it is already loaded in cache
            if (array_key_exists($name, $this->cache)) {
               return $this->cache[$name];
            }
            $this->loadDbSetting($name);
        }
        return $this->cache[$name];
    }

    public function writeSetting($name, $value) {
        $this->defines->checkSetting($name);

        if ($this->defines->isFileSetting($name)) {
            $file = $this->lock(LOCK_EX);

            try {
                $this->file->forceReload();
                $this->saveFileSetting($name, $value, $file);
            } catch (Exception $e){
                $this->unlock($file);
                throw $e;
            }
            $this->unlock($file);
            return;
        }

        if (array_key_exists($name, $this->cache) && ($this->cache[$name] == $value)) {
            Gpf_Log::debug($this->_('Setting %s have the same value %s, no change needed - skipping', $name, $value));
            return;
        }
        //store setting also to settings cache
        $this->cache[$name] = $value;
        $this->saveDbSetting($name, $value);
    }

    private function lock($operation) {
        return $this->driver->lock($this->file->getFileName(), $operation);
    }

    private function unlock($file){
        $this->driver->unlock($file);
    }

    /**
     * @return Gpf_Settings_Define
     */
    public function getSettingsDefine() {
        return $this->defines;
    }

    protected function saveDbSetting($name, $value) {
        Gpf_Db_Table_Settings::setSetting($name, $value, $this->accountId);
    }

    protected function saveFileSetting($name, $value, Gpf_Io_File $file) {
        $this->file->setSetting($name, $value, true, $file);
    }

    protected function loadDbSetting($name) {
        try {
            $this->cache[$name] = Gpf_Db_Table_Settings::getSetting($name, $this->accountId);
        } catch (Gpf_Settings_UnknownSettingException $e) {
            $this->cache[$name] = $this->defines->getDefaultValue($name);
        }
    }
    protected function loadFileSetting($name, Gpf_Io_File $file) {
        try {
            $this->cache[$name] = $this->file->getSetting($name, $file);
        } catch (Gpf_Settings_UnknownSettingException $e) {
            $this->cache[$name] = $this->defines->getDefaultValue($name);
        }
    }
}
?>
