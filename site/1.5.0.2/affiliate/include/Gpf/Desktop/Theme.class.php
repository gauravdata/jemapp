<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Desktop_Theme extends Gpf_Object {

    const CONFIG_FILE_NAME = 'theme.php';

    const ID = 'id';
    const NAME = 'name';
    const AUTHOR = 'author';
    const URL = 'url';
    const DESCRIPTION = 'description';
    const THUMBNAIL = 'thumbnail';
    const DESKTOP_MODE = 'mode';
    const DEFAULT_WALLPAPER = 'defaultWallpaper';
    const DEFAULT_WALLPAPER_POSITION = 'defaultWallpaperPosition';
    const DEFAULT_BACKGROUND_COLOR = 'defaultBackgroundColor';
    const ENABLED = 'enabled';
    const BUILT_IN = 'built_in';

    const DESKTOP_MODE_WINDOW = "W";
    const DESKTOP_MODE_SINGLE = "S";


    /**
     * @var string
     */
    private $themeId;
    /**
     * @var string
     */
    private $panelName;
    /**
     * @var Gpf_File_Config
     */
    private $configFile;

    public function __construct($themeId = '', $panelName = '') {
        $this->themeId = $themeId;
        $this->panelName = $panelName;
        if ($this->themeId == '') {
            $this->themeId = Gpf_Session::getAuthUser()->getTheme();
        }
        if ($this->panelName == '') {
            $this->panelName = Gpf_Session::getModule()->getPanelName();
        }

        $this->initThemeConfig();
    }

    public function getDesktopMode() {
        if (strtolower($this->configFile->getSetting(self::DESKTOP_MODE)) == "w" ||
        strtolower($this->configFile->getSetting(self::DESKTOP_MODE)) == "window") {
            return self::DESKTOP_MODE_WINDOW;
        }
        return self::DESKTOP_MODE_SINGLE;
    }

    /**
     * Get default wallpaper of selected theme
     *
     * @return string wallpaper file name
     */
    public function getDefaultWallpaper() {
        return $this->configFile->getSetting(self::DEFAULT_WALLPAPER);
    }

    /**
     * Get default wallpaper position of selected theme
     *
     * @return string wallpaper position
     */
    public function getDefaultWallpaperPosition() {
        try {
            return $this->configFile->getSetting(self::DEFAULT_WALLPAPER_POSITION);
        } catch (Gpf_Settings_UnknownSettingException $e) {
            return 'S';
        }
    }

    /**
     * Get default background color of selected theme
     *
     * @return string background color code
     */
    public function getDefaultBackgroundColor() {
        try {
            return $this->configFile->getSetting(self::DEFAULT_BACKGROUND_COLOR);
        } catch (Gpf_Settings_UnknownSettingException $e) {
            return '#000000';
        }
    }

    /**
     * @param Gpf_Data_RecordSet $recordset
     * @return Gpf_Data_Record
     */
    public function toRecord(Gpf_Data_RecordSet $recordset) {
        $record = $recordset->createRecord();
        $record->set(self::ID, $this->themeId);
        $this->addImageUrlToRecord($record, self::THUMBNAIL);
        $this->addValueToRecord($record, self::NAME);
        $this->addValueToRecord($record, self::AUTHOR);
        $this->addValueToRecord($record, self::URL);
        $this->addValueToRecord($record, self::DESCRIPTION);
        $this->addValueToRecord($record, self::DESKTOP_MODE);
        $record->set(self::BUILT_IN, $this->isBuiltIn());
        $record->set(self::ENABLED, $this->isEnabled());
        return $record;
    }

    private function addImageUrlToRecord(Gpf_Data_Record $record, $name) {
        try {
            $paths = Gpf_Paths::getInstance()->clonePaths($this->themeId);
            $record->set($name, $paths->getImageUrl($this->configFile->getSetting($name)));
        } catch (Gpf_Exception $e) {
            return "";
        }
    }

    private function addValueToRecord(Gpf_Data_Record $record, $name) {
        $record->set($name, $this->configFile->getSetting($name));
    }

    /**
     * @return Gpf_Io_File
     */
    public function getThemePath(){
        return new Gpf_Io_File(Gpf_Paths::getInstance()->getTopTemplatePath() .
        $this->panelName . '/' .
        $this->themeId . '/');
    }

    private function initThemeConfig() {
        $this->configFile = new Gpf_File_Config($this->getThemePath()->__toString() . self::CONFIG_FILE_NAME);
        if (!$this->configFile->isExists()) {
            throw new Gpf_Exception($this->_("Theme file (theme.php) does not exist for theme %s in directory %s", $this->themeId, $this->getThemePath()->__toString()));
        }
    }

    public function load(){
        $this->configFile->getAll();
    }

    public function setEnabled($enabled){
        $this->configFile->setSetting(self::ENABLED, $enabled ? 'Y' : 'N');
    }

    public function isEnabled(){
        if($this->configFile->hasSetting(self::ENABLED)) {
            return $this->configFile->getSetting(self::ENABLED) == 'Y';
        }
        return true;
    }

    private function isBuiltIn(){
        if($this->configFile->hasSetting(self::BUILT_IN)) {
            return $this->configFile->getSetting(self::BUILT_IN) == 'Y';
        }
        return false;
    }

    public function setBuiltIn($builtIn){
        $this->configFile->setSetting(self::BUILT_IN, $builtIn ? 'Y' : 'N');
    }

    public function setName($value){
        $this->configFile->setSetting(self::NAME, $value, false);
    }

    public function setAuthor($value){
        $this->configFile->setSetting(self::AUTHOR, $value, false);
    }

    public function setUrl($value){
        $this->configFile->setSetting(self::URL, $value, false);
    }

    public function setDescription($value){
        $this->configFile->setSetting(self::DESCRIPTION, $value, false);
    }

    public function save(){
        $this->configFile->saveAll();
    }
    
    public function setSettingsFile($path) {
        $this->configFile->setSettingsFile($path);
    }

}

?>
