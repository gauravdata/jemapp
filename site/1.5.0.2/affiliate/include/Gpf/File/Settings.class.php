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
class Gpf_File_Settings extends Gpf_File_Config {
    const SETTING_FILE_NAME = 'settings.php';
    private $accountId;

    public function __construct($accountId = null) {
        $this->accountId = $accountId;
        if ($accountId === null) {
            $settingsDirectory = Gpf_Paths::getInstance()->getAccountsPath();
        } else {
            $settingsDirectory = Gpf_Paths::getInstance()->getAccountsPath() . $accountId . '/';
        }
        parent::__construct($settingsDirectory . self::SETTING_FILE_NAME);
    }

    public function getFileName(){
        return $this->settingsFile;
    }

    protected function isAccountFileSettings() {
        if (is_null($this->accountId)) {
            return false;
        }
        return true;
    }

    protected function isSettingsFileOk(Gpf_Io_File $file) {
        $isFileOk = parent::isSettingsFileOk($file);
        if ($this->isAccountFileSettings()) {
            return $isFileOk;
        }
        return $isFileOk && $this->containsDbSettings($file);
    }

    private function containsDbSettings(Gpf_Io_File $file) {
        $content = $file->getContents();
        return strstr($content, Gpf_Settings_Gpf::DB_DATABASE) &&
        strstr($content, Gpf_Settings_Gpf::DB_HOSTNAME) &&
        strstr($content, Gpf_Settings_Gpf::DB_PASSWORD) &&
        strstr($content, Gpf_Settings_Gpf::DB_USERNAME);
    }
}

?>
