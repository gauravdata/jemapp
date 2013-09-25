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
class Gpf_Install_Requirements extends Gpf_Object {
    const MYSQL_MIN_VERSION = '4.1';
    private $requirements = array();
    private static $info;

    protected function check() {
        $this->requirements = array();
        $this->checkAccountsWritable();
        $this->checkPhpIncludePath();
        if (!defined('CHECK_MYSQL_DISABLED')) {
            $this->checkMysql();
        }
        $this->checkGdLibrary();
        if (!defined('CHECK_MODSEC_DISABLED')) {
            $this->checkModSec();
        }

        $this->checkRuntimeRequirements();
    }

    protected function checkRuntimeRequirements() {
        $this->checkMemoryLimit();
        $this->checkCompatibilityMode();
        $this->checkDisabledFunctions();
        $this->checkStandardPHPLibrary();
        $this->checkSessionAutostart();
        $this->checkSessionSavePath();
    }

    public function checkRuntime() {
        $this->checkRuntimeRequirements();
        $message = "";
        foreach ($this->requirements as $requirement) {
            if (!$requirement->isValid()) {
                $message .= $requirement->getFixDescription().', ';
            }
        }
        if($message != '') {
            die(rtrim($message, ', '));
        }
    }

    /**
     * Check if GD library is installed in php (required for e.g. Captcha images)
     *
     */
    protected function checkGdLibrary() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(extension_loaded('gd') && Gpf_Php::isFunctionEnabled('gd_info'));
        $requirement->setPositiveName($this->_('GD extension is installed'));
        $requirement->setNegativeName($this->_('GD extension is not installed'));
        $requirement->setFixDescription($this->_('Please add support of gd2 extension in your php, otherwise e.g. captcha images will not work!'));
        $this->addRequirement($requirement);
    }
    
    private function makeServiceCall($string) {
        $request = new Gpf_Rpc_DataRequest('Gpf_Install_CheckModSecRpcCaller', 'check');
        $request->setUrl(Gpf_Paths::getInstance()->getFullScriptsUrl(). 'server.php');
        
        $request->setField('teststring',$string);
        try {
            $request->sendNow();
        } catch (Gpf_Exception $e) {
            return false;
        }
        $data = $request->getData();
        if ($data->getParam('status')!='OK') {
            return false;
        }
        if ($data->getParam('recieved')!=$string) {
            return false;
        }
        return true;
    }
    
    private function checkModSecCalls() {
        //mod security check, if you need another check just add it to string below
        //example: if (!$this->makeServiceCall('select ANOTHER STRING')) {
        if (!$this->makeServiceCall('select')) {
            return false;
        }        
        return true;
    }
    
    protected function checkModSec() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setPositiveName($this->_('Server access configured properly'));
        $requirement->setNegativeName($this->_('Server access is probably not configured properly'));
        $requirement->setFixDescription($this->_('If you have Apache and mod_security module on it, it must be properly configured. If you notice some stability problems, please write to your hosting, that they turn off this module for location were PAP is installed. If you do not have Apache, then your server is probably not able to make requests to its self.'));
        $requirement->setResult($this->checkModSecCalls());
        $this->addRequirement($requirement);
    }

    protected function checkCompatibilityMode() {
        $requirement = new Gpf_Install_Requirement();
        $compatibilityMode = ini_get("zend.ze1_compatibility_mode");
        $requirement->setResult($compatibilityMode != 1 && $compatibilityMode != 'On');
        $requirement->setPositiveName($this->_('Compatibility mode is off'));
        $requirement->setNegativeName($this->_('Application requires compatibility mode off'));
        $requirement->setFixDescription($this->_('Please turn compatibility mode off in your php.ini'));
        $this->addRequirement($requirement);
    }

    protected function checkDisabledFunctions() {
        $requiredFunctions = array('tempnam', 'mkdir', 'imagettftext');
        $missingFunctions = array();
        foreach ($requiredFunctions as $function) {
            if (!Gpf_Php::isFunctionEnabled($function)) {
                $missingFunctions[] = $function;
            }
        }
        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(count($missingFunctions) == 0);
        $requirement->setPositiveName($this->_('All required functions are enabled'));
        $requirement->setNegativeName($this->_('Following requires functions are not enabled or available: %s', implode(',', $missingFunctions)));
        $requirement->setFixDescription($this->_('Please enable following functions in your php.ini: %s', implode(',', $missingFunctions)));
        $this->addRequirement($requirement);
    }

    /**
     * Check memory limit of php
     *
     */
    protected function checkMemoryLimit() {
        $requirement = new Gpf_Install_Requirement();
        if (self::getMemoryLimit() < 33554432) {
            @ini_set('memory_limit', '32M');
        }
        $requirement->setResult(self::getMemoryLimit() >= 33554432);
        $requirement->setPositiveName($this->_('Memory limit is %s bytes', self::getMemoryLimit()));
        $requirement->setNegativeName($this->_('Please increase memory_limit parameter to 32M in your php.ini'));
        $requirement->setFixDescription($this->_('Application require minimum 32MB of memory'));
        $this->addRequirement($requirement);
    }

    /**
     * Compute current memory limit of php
     *
     * @return int
     */
    public static function getMemoryLimit() {
        $memoryLimit = ini_get('memory_limit');

        if (!strlen(trim($memoryLimit)) || $memoryLimit <= 0) {
            $memoryLimit = '10g';
        }
        $last = strtolower($memoryLimit{strlen($memoryLimit)-1});
        switch($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        return $memoryLimit;
    }
    
    protected function checkStandardPHPLibrary() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(Gpf_Php::isExtensionLoaded('SPL'));
        $requirement->setPositiveName($this->_('Standard PHP Library is on'));
        $requirement->setNegativeName($this->_('Application requires Standard PHP Library extension'));
        $requirement->setFixDescription($this->_('Please recompile your PHP with Standard PHP Library extension'));
        $this->addRequirement($requirement);
    }
    
    protected function checkSessionAutostart() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(ini_get('session.auto_start') == 0 || ini_get('session.auto_start') == false);
        $requirement->setPositiveName($this->_('Session autostart is off'));
        $requirement->setNegativeName($this->_('Application requires session.auto_start parameter off'));
        $requirement->setFixDescription($this->_('Please turn session.auto_start parameter off in your php.ini'));
        $this->addRequirement($requirement);
    }
    
    protected function checkSessionSavePath() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(substr(ini_get('session.save_path'),0,2) != './');
        $requirement->setPositiveName($this->_('Session path is setup correctly'));
        $requirement->setNegativeName($this->_('Session path can not be set to script start path for this application'));
        $requirement->setFixDescription($this->_('Session path is setup incorrectly for this application. Please set session.save_path parameter to for example /tmp (unite to all scripts) in your php.ini'));
        $this->addRequirement($requirement);
    }

    private function checkAccountsWritable() {
        $requirement = new Gpf_Install_Requirement();
        $requirement->setPositiveName($this->_('Configuration directory is writable'));
        $requirement->setNegativeName($this->_('Configuration directory has to be writable'));

        $accountDirectory = Gpf_Paths::getInstance()->getAccountsPath();
        $result = (@is_dir($accountDirectory) && is_writable($accountDirectory));

        if($result) {
            $testFile = new Gpf_Io_File($accountDirectory . 'check');
            $subTestFile = new Gpf_Io_File($accountDirectory . 'check/subcheck');
            try {
                $testFile->open('w');
                $testFile->close();
                $testFile->delete();
            } catch (Exception $e) {
                $result = false;
                $requirement->setNegativeName($this->_('Could not create file inside %s directory', $accountDirectory));
            }
            try {
                $testFile->mkdir();
                $testFile->rmdir();
            } catch (Exception $e) {
                $result = false;
                $requirement->setNegativeName($this->_('Could not create directory inside %s directory', $accountDirectory));
            }
            try {
                $testFile->mkdir();
                $subTestFile->open('w');
                $subTestFile->close();
                $subTestFile->delete();
                $subTestFile->mkdir();
                $subTestFile->rmdir();
                $testFile->rmdir();
            } catch (Exception $e) {
                $result = false;
                $requirement->setNegativeName($this->_('Could not create file or directory inside %s subdirectory. Probably safe mode is not properly configured.', $accountDirectory));
            }
        }

        $requirement->setResult($result);
        $description = $this->_('Please make directory %s and all subdirectories writable by webserver.', $accountDirectory);

        if(stripos(PHP_OS, 'win') === false) {
            $description .= $this->_('On unix-like systems you can type "chmod -R 777 %s".', $accountDirectory);
        }

        $description .= $this->_('On any system you can set write permissions using your favourite FTP client.');
        $requirement->setFixDescription($description);
        $this->addRequirement($requirement);
    }

    private function checkPhpIncludePath() {
        try {
            Gpf_Paths::getInstance()->setIncludePath();
            return;
        } catch (Exception $e) {
        }

        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult(false);
        $requirement->setPositiveName($this->_('PHP include path'));
        $requirement->setNegativeName($this->_('Could not set PHP include path'));

        $description = $this->_('Please configure your PHP so that script is able to change include_path.');
        $description .= $this->_('Alternatively you can set include_path directly in your php.ini. include_path=%s', Gpf_Paths::getInstance()->getIncludePath());
        $requirement->setFixDescription($description);
        $this->addRequirement($requirement);
    }

    private function checkMysql() {
        $mysqlSupport = Gpf_Php::isFunctionEnabled('mysql_connect');

        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult($mysqlSupport);
        $requirement->setPositiveName($this->_('MySQL extension is installed'));
        $requirement->setNegativeName($this->_('MySQL extension is not installed'));
        $requirement->setFixDescription($this->_('Please enable MySQL extension. More info http://php.net/mysql'));
        $this->addRequirement($requirement);
        if(!$mysqlSupport) {
            return;
        }

        $mysqlVersion = $this->getMysqlVersion();
        if($mysqlVersion === false) {
            return;
        }
        $mysqlVersionTest = (version_compare($mysqlVersion, self::MYSQL_MIN_VERSION) >= 0);

        $requirement = new Gpf_Install_Requirement();
        $requirement->setResult($mysqlSupport);
        $requirement->setPositiveName($this->_('MySQL version is %s or higher', self::MYSQL_MIN_VERSION));
        $requirement->setNegativeName($this->_('MySQL version is less then %s', self::MYSQL_MIN_VERSION));
        $requirement->setFixDescription($this->_('Please install MySQL version %s or higher. Your current version is %s. More info http://myqsl.net/',
        self::MYSQL_MIN_VERSION, $mysqlVersion));
        $this->addRequirement($requirement);
    }
    
    private function parseVersion($text) {
        $value = stristr($text, 'Client API version');

        if(1 == preg_match('/[1-9].[0-9].[1-9][0-9]/', $value, $match)) {
            return $match[0];
        }
        return false;
    }

    protected function getMysqlVersion() {        
        if(self::$info === null) {
            //first we try to get info through special file because phpinfo with ob_start may cause problems/internal server errors on some servers            
            self::$info = file_get_contents(Gpf_Paths::getInstance()->getFullBaseServerUrl() . Gpf_Paths::SCRIPTS_DIR . 'modulesinfo.php');
            $version = $this->parseVersion(self::$info);
            if ($version !== false) {
                return $version; 
            }            
            ob_start();
            phpinfo(INFO_MODULES);
            self::$info = ob_get_contents();
            ob_end_clean();
        }
        return $this->parseVersion(self::$info);
    }

    protected function addRequirement(Gpf_Install_Requirement $requirement) {
        $this->requirements[] = $requirement;
    }

    public function getRequirements() {
        $this->check();
        return $this->requirements;
    }

    public function isValid() {
        $this->check();
        foreach ($this->requirements as $requirement) {
            if(!$requirement->isValid()) {
                return false;
            }
        }
        return true;
    }
}

class Gpf_Install_Requirement extends Gpf_Object {
    private $result = false;
    private $positiveName = '';
    private $negativeName = '';

    private $fixDescription = '';

    public function setResult($result) {
        $this->result = $result;
    }

    public function setFixDescription($description) {
        $this->fixDescription = $description;
    }

    public function getFixDescription() {
        return $this->fixDescription;
    }

    public function setPositiveName($name) {
        $this->positiveName = $name;
    }

    public function getName() {
        if($this->result) {
            return $this->positiveName;
        }
        return $this->negativeName;
    }

    public function setNegativeName($name) {
        $this->negativeName = $name;
    }

    public function isValid() {
        return $this->result;
    }
}

?>
