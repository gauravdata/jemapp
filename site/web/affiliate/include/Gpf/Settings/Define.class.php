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
abstract class Gpf_Settings_Define extends Gpf_Object {
    const FILE = 'F';
    const DB = 'D';
    
    const LARGE_TEXT_SETTINGS_DIR = 'settings';
    const LARGE_TEXT_SETTING_TEMPLATE_FILE_EXTENSION = 'stpl';
    
    /**
     * array of setting types, indexed by setting name
     * each array item contains subarray with indexes
     * 'name' - setting name
     * 'defaultValue' - setting default value or null
     * 'type' - type of setting: file or DB
     * 'isAccount' - true or false
     */
    private $settings = array();
    
    public function init() {
    	$this->defineFileSettings();
    	$this->defineDbSettings();
    }

    public function writeDefaultFileSettings() {
    	foreach ($this->settings as $name => $value) {
    		if ($this->settings[$name]['type'] == self::FILE) {
    		    try {
    			    Gpf_Settings::set($name, Gpf_Settings::get($name), true);
    		    } catch (Gpf_Settings_UnknownSettingException $e) {
    		    }
    		}
    	}
    }
    
    public function addFileSetting($settingName, $defaultValue = null, $isAccountSetting = false) {
        $this->addSetting($settingName, $defaultValue, self::FILE, $isAccountSetting);
    }
    
    public function addDbSetting($settingName, $defaultValue = null, $isAccountSetting = false) {
        $this->addSetting($settingName, $defaultValue, self::DB, $isAccountSetting);
    }
    
    protected function addDefaultValue($name, $value) {
        if(!array_key_exists($name, $this->settings)) {
            throw new Gpf_Exception("Setting '$name' is not known, define it first in defineSettings() function!");
        }
        $this->settings[$name]['defaultValue'] = $value;
    }
    
    private function addSetting($settingName, $defaultValue, $type, $isAccountSetting) {
        $temp = array();
        $temp['name'] = $settingName;
        $temp['defaultValue'] = $defaultValue;
        $temp['type'] = $type;
        $temp['isAccount'] = $isAccountSetting;
        
        $this->settings[$settingName] = $temp;
    }

    public function getDefaultValue($name) {
        $defaultValue = $this->settings[$name]['defaultValue'];
        if($defaultValue === null) {
            $this->lazyInitDefaultValue($name);
            $defaultValue = $this->settings[$name]['defaultValue'];
        }
        if($defaultValue === null) {
            throw new Gpf_Settings_UnknownSettingException($name);
        }
        return $defaultValue;
    }
    
    public function isFileSetting($name) {
        return $this->settings[$name]['type'] == self::FILE;
    }
    
    public function isAccountSetting($name) {
    	return $this->settings[$name]['isAccount'] == true;
    }
    
    public function checkSetting($name) {
        if(!array_key_exists($name, $this->settings) || !is_array($this->settings[$name])) {
            throw new Gpf_Exception("Setting '$name' is not known, define it first in defineSettings() function!");
        }
    }
    
    protected function lazyInitDefaultValue($name) {
        try {
            $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getResourcePath($name .
            '.' . self::LARGE_TEXT_SETTING_TEMPLATE_FILE_EXTENSION, self::LARGE_TEXT_SETTINGS_DIR));
        } catch (Gpf_ResourceNotFoundException $e) {
            return;
        }
        $this->addDefaultValue($name, $file->getContents());
    }
    
    abstract protected function defineFileSettings();
    
    abstract protected function defineDbSettings();
}
?>
