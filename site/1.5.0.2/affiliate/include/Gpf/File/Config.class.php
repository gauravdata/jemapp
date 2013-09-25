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
class Gpf_File_Config {
    protected $settingsFile;
    private $parameters = array();
    private $initialized = false;

    public function __construct($settingsFile) {
        $this->settingsFile = $settingsFile;
    }

    /**
     * @return array
     */
    public function getAll(Gpf_Io_File $file = null) {
        if(!$this->initialized) {
            $this->parameters = $this->readSettingsValues($file);
            $this->initialized = true;
        }
        return $this->parameters;
    }

    public function saveAll() {
        $this->writeSettingsValues();
    }

    public function hasSetting($name) {
        $this->getAll();
        return array_key_exists($name, $this->parameters);
    }

    public function forceReload($value = false) {
        $this->initialized = $value;
    }

    public function getSetting($name, Gpf_Io_File $file = null) {
        $this->getAll($file);
        if(array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        throw new Gpf_Settings_UnknownSettingException($name);
    }

    public function getSettingWithDefaultValue($name, $defaultValue) {
        // obsolete
        // to be deleted
        $this->getAll();
        if(array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        return $defaultValue;
    }

    public function setSetting($name, $value, $flush = true, Gpf_Io_File $file = null) {
        $this->getAll($file);
        if(array_key_exists($name, $this->parameters) && $this->parameters[$name] == $value) {
            return;
        }
        $this->parameters[$name] = $value;
        if($flush) {
            $this->writeSettingsValues($file);
        }
    }

    public function getSettingFileName() {
        return $this->settingsFile;
    }

    public function isExists() {
        $file = new Gpf_Io_File($this->settingsFile);
        return $file->isExists();
    }

    public function removeSetting($settingName, $flush = true) {
        if (!$this->hasSetting($settingName)) {
            return;
        }
        unset($this->parameters[$settingName]);
        if ($flush) {
            $this->writeSettingsValues();
        }
    }

    private function readSettingsValues(Gpf_Io_File $file = null) {
        if (is_null($file)) {
            $file = new Gpf_Io_File($this->settingsFile);
        }
        if(!$file->isExists()) {
           throw new Gpf_Exception($file->getFileName() . ' not exists.');
        }
        $file->open();

        $values = array();
        $lines = $file->readAsArray();

        foreach($lines as $line) {
            if(false !== strpos($line, '<?') || false !== strpos($line, '?>')) {
                continue;
            }
            $pos = strpos($line, '=');
            if($pos === false) {
                continue;
            }
            $name = substr($line, 0, $pos);
            $value = substr($line, $pos + 1);
            $values[$name] = rtrim($value);
        }
        return $values;
    }

    protected function isSettingsFileOk(Gpf_Io_File $file) {
        try {
            return ($file->getSize() > 0) || ($this->getFileDataLength($file) > 0);
        } catch (Exception $e) {
            return false;
        }
    }

    private function getFileDataLength(Gpf_Io_File $file) {
        $data = file_get_contents($file->getFileName());
        return strlen($data);
    }

    private function writeSettingsValues(Gpf_Io_File $settingsFile = null) {
        $settingsTmpFile = new Gpf_Io_File($this->settingsFile . '_' . microtime() .'.tmp');

        $this->writeSettingToFile($settingsTmpFile);

        if ($this->isSettingsFileOk($settingsTmpFile)) {
            try {
                if (is_null($settingsFile)) {
                    $settingsFile = new Gpf_Io_File($this->settingsFile);
                }
                $this->copyFile($settingsTmpFile, $settingsFile, 0777);
                $settingsTmpFile->delete();
            } catch (Exception $e) {
                try {
                    $this->writeSettingToFile($settingsFile);

                    if ($this->isSettingsFileOk($settingsFile)) {
                        $settingsTmpFile->delete();
                    } else {
                        throw new Gpf_Exception('Unable to save settings file! (Temp file is OK: '. $settingsTmpFile->getFileName().')');
                    }
                } catch (Exception $e) {
                    throw $e;
                }
            }
        } else {
            $settingsTmpFile->delete();
            throw new Gpf_Exception('Unable to save settings file!');
        }
    }

    private function writeSettingToFile(Gpf_Io_File $file) {
        $file->setFilePermissions(0777);

        if (defined('ENABLE_ENGINECONFIG_LOG')) {
            Gpf_Log::info('(writeSettingsValues - before write) file ' . @$file->getFileName() . ' size: ' . @$file->getSize() . ', permissions: ' . @$file->getFilePermissions() . ', owner: ' . @$file->getFileOwner());
        }
        $file->open('w');

        $text = '<?php /*' . "\n";
        foreach($this->parameters as $key => $value) {
            $text .= $key . '=' . $value . "\r\n";
        }
        $text .= '*/ ?>';
        $file->write($text);
        $file->close();

        if (defined('ENABLE_ENGINECONFIG_LOG')) {
            Gpf_Log::info('(writeSettingsValues - after write) file ' . @$file->getFileName() . ' size: ' . @$file->getSize() . ', permissions: ' . @$file->getFilePermissions() . ', owner: ' . @$file->getFileOwner());
        }
    }

    protected function copyFile(Gpf_Io_File $source, Gpf_Io_File $target, $mode = null) {
        $target->open('w');
        $target->write($source->getContents());
        if($mode !== null) {
            @chmod($target->getFileName(), $mode);
        }
    }

    public function setSettingsFile($path) {
        $this->settingsFile = $path;
    }
}

?>
