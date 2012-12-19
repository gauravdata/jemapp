<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
class Gpf_Install_RecommendedSettings extends Gpf_Object {
    private $settings = array();

    public function __construct() {
    }

    protected function add($name, $recommended, $current) {
        $this->settings[] = new Gpf_Install_RecommendedSetting($name, $recommended, $current);
    }
    
    protected function addPhpIniCheck($settingName, $recommendedValue, $iniName) {
        $this->add($settingName . " ($iniName)", $recommendedValue, ini_get($iniName));
    }
    
    public function getSettings() {
        return $this->settings;
    }
}

class Gpf_Install_RecommendedSetting extends Gpf_Object {
    private $name;
    private $recommended;
    private $current;

    public function __construct($name, $recommended, $current) {
        $this->name = $name;
        $this->recommended = $recommended;
        $this->current = $current;
    }
    
    public function isRecommended() {
        return $this->recommended == $this->current;    
    }
    
    public function getName() {
        return $this->name;    
    }
    
    public function getRecommendedAsText() {
        return $this->recommended ? $this->_('On') : $this->_('Off');    
    }
    
    public function getCurrentAsText() {
        return $this->current ? $this->_('On') : $this->_('Off');    
    }
    
    public function getRecommended() {
        return $this->recommended;    
    }
    
    public function getCurrent() {
        return $this->current;    
    }
}

?>
