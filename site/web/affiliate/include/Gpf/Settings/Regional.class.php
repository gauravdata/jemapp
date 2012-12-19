<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Matej Kendera
 *   @since Version 1.0.0
 *   $Id: EmailSettingsForms.class.php 25470 2009-09-25 10:05:33Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Settings_Regional {

    /**
     * @var Gpf_Settings_Regional
     */
    private static $instance;

    private $thousandsSeparator, $decimalSeparator, $dateFormat, $timeFormat;

    /**
     * @return Gpf_Settings_Regional
     */
    public function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Gpf_Settings_Regional();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->loadSettings();
    }

    public function getThousandsSeparator() {
        return $this->thousandsSeparator;
    }

    public function getDecimalSeparator() {
        return $this->decimalSeparator;
    }

    public function getDateFormat() {
        return $this->dateFormat;
    }
    
    public function setDateFormat($format) {
        $this->dateFormat = $format;
    }

    public function getTimeFormat() {
        return $this->timeFormat;
    }

    private function loadSettings() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT) == Gpf::YES) {
            try {
                $this->loadSettingsFromLanguage();
                return;
            } catch (Gpf_Exception $e) {
            }
        }
        $this->thousandsSeparator = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR);
        $this->decimalSeparator = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR);
        $this->dateFormat = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT);
        $this->timeFormat = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT);
    }

    /**
     * @throws Gpf_Exception
     */
    private function loadSettingsFromLanguage() {
        $lang = Gpf_Lang_Dictionary::getInstance()->getLanguage();
        if ($lang == null) {
            throw new Gpf_Exception('No language loaded');
        }
        $this->thousandsSeparator = $lang->getThousandsSeparator();
        $this->decimalSeparator = $lang->getDecimalSeparator();
        $this->dateFormat = $lang->getDateFormat();
        $this->timeFormat = $lang->getTimeFormat();
    }

}
?>
