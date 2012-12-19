<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak, Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: ApplicationSettings.class.php 27426 2010-03-01 13:48:31Z mjancovic $
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
abstract class Gpf_ApplicationSettings extends Gpf_Object {
    
    /**
     * @var Gpf_Data_RecordSet
     */
    private $recordSet;
    
    const CODE = "code";
    const VALUE = "value";
    
    protected function loadSetting() {
        $this->addValue("theme", Gpf_Session::getAuthUser()->getTheme());
        $this->addValue("date_time_format", 'MM/d/yyyy HH:mm:ss');
        $this->addValue("programVersion", Gpf_Application::getInstance()->getVersion());
        $this->addValue(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES, Gpf_Settings::get(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES));
        
        $quickLaunchSettings = new Gpf_Desktop_QuickLaunch();
    	$this->addValue(Gpf_Desktop_QuickLaunch::SHOW_QUICK_LAUNCH, $quickLaunchSettings->getShowQuickLaunch());
        
        $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR, Gpf_Settings_Regional::getInstance()->getThousandsSeparator());
        $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR, Gpf_Settings_Regional::getInstance()->getDecimalSeparator());
        $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT, Gpf_Settings_Regional::getInstance()->getDateFormat());
        $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT, Gpf_Settings_Regional::getInstance()->getTimeFormat());
        
        Gpf_Plugins_Engine::extensionPoint('Core.loadSetting', $this);
    }
    
    /**
     * @anonym
     * @service
     */
    public function getSettings(Gpf_Rpc_Params $params) {
        return $this->getSettingsNoRpc();
    }
    
    /**
     *
     * @return Gpf_Data_RecordSet
     */
    public function getSettingsNoRpc() {
        $this->recordSet = new Gpf_Data_RecordSet();
        $this->recordSet->setHeader(new Gpf_Data_RecordHeader(array(self::CODE, self::VALUE)));
        $this->loadSetting();
        return $this->recordSet;
    }
    
    public function addValue($code, $value) {
        $record = $this->recordSet->createRecord();
        $record->set(self::CODE, $code);
        $record->set(self::VALUE, $value);
        $this->recordSet->addRecord($record);
    }
    
    protected function addSetting($code) {
        $this->addValue($code, Gpf_Settings::get($code));
    }
}
?>
