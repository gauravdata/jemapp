<?php
/** *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o. *   @author Quality Unit *   @package Core classes *   @since Version 1.0.0 *    *   Licensed under the Quality Unit, s.r.o. Dual License Agreement, *   Version 1.0 (the "License"); you may not use this file except in compliance *   with the License. You may obtain a copy of the License at *   http://www.qualityunit.com/licenses/gpf *    */

if (!class_exists('Gpf_Object', false)) {
  class Gpf_Object {
  
      /**
       * @return Gpf_DbEngine_Database
       */
      protected function createDatabase() {
          return Gpf_DbEngine_Database::getDatabase();
      }
  
      /**
       * Translate input message into selected language.
       * If translation will not be found, return same message.
       *
       * @param string $message
       * @return string
       */
      public function _($message) {
          $args = func_get_args();
          return Gpf_Lang::_($message, $args);
      }
      
      /**
       * Translates text enclosed in ##any text##
       * This function is not parsed by language parser, because as input should be used e.g. texts loaded from database
       *
       * @param string $message String to translate
       * @return string Translated text
       */
      public function _localize($message) {
          return Gpf_Lang::_localizeRuntime($message);
      }
      
      /**
       * Translate input message into default language defined in language settings for account.
       * This function should be used in case message should be translated to default language (e.g. log messages written to event log)
       *
       * @param string $message
       * @return string
       */
      public function _sys($message) {
          $args = func_get_args();
          return Gpf_Lang::_sys($message, $args);
      }
  }

} //end Gpf_Object

if (!class_exists('Gpf_Install_Requirements', false)) {
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
  

} //end Gpf_Install_Requirements

if (!class_exists('Pap_Install_Requirements', false)) {
  class Pap_Install_Requirements extends Gpf_Install_Requirements {
  }
  

} //end Pap_Install_Requirements

if (!class_exists('Gpf_Lang', false)) {
  class Gpf_Lang {
      
      /**
       * Translate input message into selected language.
       * If translation will not be found, return same message.
       *
       * @param string $message
       * @return string
       */
      public static function _($message, $args = null, $langCode = '') {
          if (!is_array($args)) {
              $args = func_get_args();
          }
          $dictionary = Gpf_Lang_Dictionary::getInstance($langCode);
          return self::_replaceArgs($dictionary->get($message), $args);
      }
      
      /**
       * Replace arguments in message.
       *
       * @param string $message
       * @param $args
       * @return string
       */
      public static function _replaceArgs($message, $args = null) {
          if (!is_array($args)) {
              $args = func_get_args();
          }
          if(count($args) > 1 && substr_count($message, '%s') < count($args)) {
              array_shift($args);
              return vsprintf($message, $args);
          }
          return $message;
      }
      
      /**
       * Translate input message into default language defined in language settings for account.
       * This function should be used in case message should be translated to default language (e.g. log messages written to event log)
       *
       * @param string $message
       * @return string
       */
      public static function _sys($message, $args = null) {
          if (!is_array($args)) {
              $args = func_get_args();
          }
          $dictionary = Gpf_Lang_Dictionary::getInstance(Gpf_Lang_Dictionary::getDefaultSystemLanguage());
          return self::_replaceArgs($dictionary->get($message), $args);
      }
      
      /**
       * Encapsulate message as translated message with ## ##
       *
       * @param string $message
       * @return string
       */
      public static function _runtime($message) {
          return '##' . $message . '##';
      }
      
      /**
       * Translates text enclosed in ##any text##
       * This function is not parsed by language parser, because as input should be used e.g. texts loaded from database
       *
       * @param string $message String to translate
       * @return string Translated text
       */
      public static function _localizeRuntime($message, $langCode = '') {
          preg_match_all('/##(.+?)##/ms', $message, $attributes, PREG_OFFSET_CAPTURE);
          foreach ($attributes[1] as $index => $attribute) {
              $message = str_replace($attributes[0][$index][0], self::_($attribute[0], null, $langCode), $message);
          }
          return $message;
          
      }
  }
  

} //end Gpf_Lang

if (!class_exists('Gpf_Lang_Dictionary', false)) {
  class Gpf_Lang_Dictionary extends Gpf_Object {
      const LANGUAGE_DIRECTORY = 'lang/';
      const LANGUAGE_REQUEST_PARAMETER = 'l';
  
      /**
       * Array of language dictonary instances. For each language code can be here own instance.
       *
       * @var array
       */
      protected static $instances = array();
  
      /**
       * @var Gpf_Lang_Language
       */
      private $language;
  
      protected function __construct() {
      }
  
      /**
       * @param string $langCode language code for which you need instance
       * @return Gpf_Lang_Dictionary
       */
      public static function getInstance($langCode = '') {
          if(!array_key_exists($langCode, self::$instances)) {
              self::$instances[$langCode] = new Gpf_Lang_Dictionary();
              if ($langCode != '') {
                  try {
                      self::$instances[$langCode]->load($langCode);
                  } catch (Exception $e) {
                  }
              }
              setlocale(LC_ALL, 'en_US.UTF-8');
          }
          return self::$instances[$langCode];
      }
  
      /**
       * Compute default language in following order:
       * 1. try if language parameter is not set in request
       * 2. try if cookie doesn't contain language selection from the past
       * 3. try load language settings from browser preferences
       * 4. load default system language
       *
       * @return string Default language code
       */
      public static function getDefaultLanguage() {
          //try if language was not defined by language parameter in request
          if (isset($_REQUEST[self::LANGUAGE_REQUEST_PARAMETER]) && self::isLanguageSupported($_REQUEST[self::LANGUAGE_REQUEST_PARAMETER]) ) {
              return $_REQUEST[self::LANGUAGE_REQUEST_PARAMETER];
          }
  
          //try if language was not defined in cookie parameter
          if (isset($_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE]) &&
          self::isLanguageSupported($_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE])) {
              return $_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE];
          }
  
          //try load language from browser
          if (($acceptLang = Gpf_Lang_Dictionary::getBrowserLanguage()) !== false) {
              return $acceptLang;
          }
  
          //use default system language
          return self::getDefaultSystemLanguage();
      }
  
      public static function getDefaultSystemLanguage() {
          try {
              $defaultLanguage = Gpf_Db_Table_Languages::getInstance()->getDefaultLanguage();
              $langCode = $defaultLanguage->getCode();
              if ($langCode != null) {
                  return $langCode;
              }
          } catch (Exception $e) {
          }
          return Gpf_Application::getInstance()->getDefaultLanguage();
      }
  
      public static function isLanguageSupported($langCode) {
          static $languages;
          if ($languages == null) {
              try {
                  $languagesObj = Gpf_Lang_Languages::getInstance();
                  $languages = $languagesObj->getActiveLanguagesNoRpc();
              } catch (Exception $e) {
                  return false;
              }
          }
          return $languages->existsRecord($langCode);
      }
  
      /**
       * Get first supported language browser
       *
       * @return string If none supported language was found, return false
       */
      private static function getBrowserLanguage() {
          if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
              $languages = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
              foreach($languages as $language) {
                  $arrLang = explode(';', $language);
                  $langCode = self::decodeLanguageCode($arrLang[0]);
                  if (self::isLanguageSupported($langCode)) {
                      return $langCode;
                  }
              }
          }
          return false;
      }
  
      /**
       * @param String $browserLangCode
       * @return String
       */
      public static function decodeLanguageCode($browserLangCode) {
          $langCode = strtolower($browserLangCode);
          if (strlen($browserLangCode) > 2) {
              $langCode = substr($langCode, 0, 2) . strtoupper(substr($browserLangCode, 2));
          }
          return $langCode;
      }
  
      protected function isSupportedLanguage($languageCode) {
          return self::isLanguageSupported($languageCode);
      }
  
      public function load($languageCode) {
          if (!$this->isSupportedLanguage($languageCode)) {
              $languageCode = self::getDefaultLanguage();
          }
          $language = new Gpf_Lang_Language($languageCode);
          $language->load();
          $this->language = $language;
          self::$instances[$languageCode] = $this;
          return $languageCode;
      }
  
      public function getEncodedClientMessages() {
          if ($this->getLanguage() != null) {
              $langCode = $this->getLanguage()->getCode();
          } else {
              $langCode = $this->getDefaultSystemLanguage();
          }
          $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getLanguageCacheDirectory()
          . Gpf_Application::getInstance()->getCode() . '_' .
          $langCode . '.c.php');
          return $file->getContents();
      }
  
      /**
       *
       * @return Gpf_Data_RecordSet
       */
      public function getClientMessages() {
          if($this->language === null) {
              $this->load(Gpf_Session::getAuthUser()->getLanguage());
          }
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->setHeader(array('source', 'translation'));
  
          foreach ($this->language->getClientMessages() as $source => $translation) {
              $recordSet->add(array($source, $translation));
          }
          return $recordSet;
      }
  
      public function get($message) {
          if($this->language === null) {
              return $message;
          }
          return $this->language->localize($message);
      }
  
      /**
       * return language definition
       *
       * @return Gpf_Lang_Language
       */
      public function getLanguage() {
          return $this->language;
      }
  }

} //end Gpf_Lang_Dictionary

if (!class_exists('Gpf_Php', false)) {
  class Gpf_Php {
  
      /**
       * Check if function is enabled and exists in php
       *
       * @param $functionName
       * @return boolean Returns true if function exists and is enabled
       */
      public static function isFunctionEnabled($functionName) {
          if (function_exists($functionName) && strstr(ini_get("disable_functions"), $functionName) === false) {
              return true;
          }
          return false;
      }
      
      /**
       * Check if extension is loaded
       * 
       * @param $extensionName
       * @return boolean Returns true if extension is loaded
       */
      public static function isExtensionLoaded($extensionName) {
          return extension_loaded($extensionName);
      }
  
  }

} //end Gpf_Php

if (!class_exists('Gpf_Application', false)) {
  abstract class Gpf_Application extends Gpf_Object {
      protected $installedVersion;
      private $gpfInstalledVersion;
  
      protected $rolePrivileges = array();
  
      /**
       * @var Gpf_Application
       */
      private static $instance;
  
      public static function create(Gpf_Application $application) {
          setlocale(LC_ALL, 'en.UTF-8');
          self::$instance = $application;
          self::$instance->registerRolePrivileges();
          self::$instance->initLogger();
          self::$instance->addSmartyPluginsDir();
          $timezone = Gpf_Settings_Gpf::DEFAULT_TIMEZONE;
          try {
              $timezone = Gpf_Settings::get(Gpf_Settings_Gpf::TIMEZONE_NAME);
          } catch (Gpf_Exception $e) {
              Gpf_Log::error('Unable to load timezone: %s - using default one.', $e->getMessage());
          }
          if(false === @date_default_timezone_set($timezone)) {
              Gpf_Log::error('Unable to set timezone %s:', $timezone);
          }
      }
  
      public function getDefaultLanguage() {
          return 'en-US';
      }
  
      /**
       * @return Gpf_Application
       */
      public static function getInstance() {
          if(self::$instance === null) {
              throw new Gpf_Exception('Application not initialize');
          }
          return self::$instance;
      }
  
      /**
       * @return String
       */
      public function getApiFileName() {
          throw new Gpf_Exception('Api is not supported');
      }
  
      public function createSettings() {
          return new Gpf_Settings_Gpf();
      }
  
      protected function addSmartyPluginsDir() {
          Gpf_Paths::getInstance()->addSmartyPluginPath(Gpf_Paths::getInstance()->getFrameworkPath() . 'include/Gpf/SmartyPlugins');
      }
  
      public function getInstalledVersion($gpf = false) {
          if($this->installedVersion === null) {
              $this->computeInstalledVersions();
          }
          if($gpf) {
              return $this->gpfInstalledVersion;
          }
          return $this->installedVersion;
      }
  
      public function getHelpUrl() {
          return '';
      }
  
      public static function getKnowledgeHelpUrl($path) {
          return self::getInstance()->getHelpUrl() . $path;
      }
  
      public function getAccountId() {
          return Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
      }
  
      private function computeInstalledVersions() {
          $this->installedVersion = false;
          $this->gpfInstalledVersion = false;
          try {
              $this->installedVersion = $this->computeLatestInstalledApplicationVersion();
              $this->gpfInstalledVersion = Gpf_Db_Table_Versions::getInstance()->getLatestVersion(Gpf::CODE);
          } catch (Gpf_DbEngine_Exception $e) {
          	throw new Gpf_DbEngine_Exception($e->getMessage());
          } catch (Gpf_Exception $e) {
          	Gpf_Log::debug('Error during computing latest versions: ' . $e->getMessage());
          }
      }
  
      protected function computeLatestInstalledApplicationVersion() {
          return $this->installedVersion = Gpf_Db_Table_Versions::getInstance()->getLatestVersion($this->getCode());
      }
  
      public function isInstalled() {
          return $this->getInstalledVersion() !== false;
      }
  
      private static function getVersionWithoutBuild($version) {
          $parts = explode('.', $version);
          if(count($parts) <=3 ) {
              return $version;
          }
          return implode('.', array($parts[0], $parts[1], $parts[2]));
      }
  
      private function equalsVersions($version1, $version2) {
          if(Gpf_Paths::getInstance()->isDevelopementVersion()) {
              return self::getVersionWithoutBuild($version1) == self::getVersionWithoutBuild($version2);
          }
          return $version1 == $version2;
      }
  
      public function isUpdated() {
          return $this->equalsVersions($this->getVersion(),$this->getInstalledVersion())
          && $this->equalsVersions(Gpf::GPF_VERSION, $this->getInstalledVersion(true));
      }
  
      final public function isInMaintenanceMode() {
          try {
            return !$this->isInstalled() || !$this->isUpdated();
          } catch (Gpf_DbEngine_Exception $e) {
              Gpf_log::debug('Database error occured while computing latest installed application version: ' . $e->getMessage());
              return false;
          }
      }
  
      protected function readStatFile($file) {
          if (!file_exists($file) || !is_readable($file)) {
              throw new Gpf_Exception('Failed to read file ' . $file);
          }
          return @file_get_contents($file);
      }
  
      protected function getCpuCount() {
          $cpuinfo = $this->readStatFile('/proc/cpuinfo');
          preg_match_all('/processor\s*?:\s([0-9]*)/ms' ,$cpuinfo ,$matches);
          if (is_array($matches) && array_key_exists(1, $matches) && is_array($matches[1]) && count($matches[1]) > 0) {
              $maxCpuNr = $matches[1][count($matches[1]) - 1];
              if (strlen($maxCpuNr)) {
                  return $maxCpuNr + 1;
              }
          }
          throw new Gpf_Exception('Failed to read cpuinfo');
      }
  
      protected function getMaxLoad() {
          return max($this->getCpuCount()/2, Gpf_Settings::get(Gpf_Settings_Gpf::MAX_ALLOWED_SERVER_LOAD));
      }
  
      public function isServerOverloaded() {
          try {
              return max($this->getServerLoad(1), $this->getServerLoad(5)) > $this->getMaxLoad();
          } catch (Exception $e) {
              return false;
          }
      }
  
      protected function getServerLoad($time = 1) {
          $loads = preg_split("/ /",$this->readStatFile('/proc/loadavg'));
          $load = false;
          switch ($time) {
              case 1:
                  $load =  $loads[0];
                  break;
              case 5:
                  $load =  $loads[1];
                  break;
              case 10:
                  $load =  $loads[2];
                  break;
              default:
                  $load =  $loads[0];
          }
          if (is_numeric($load)) {
              return $load;
          }
          throw new Gpf_Exception('Failed to read server load');
      }
  
      abstract public function getVersion();
      abstract public function getCode();
  
      /**
       * Each application should define set of default roles and privileges classes
       * use function addRolePrivileges to register role
       */
      abstract public function registerRolePrivileges();
  
      protected function initLogger() {
      }
  
      /**
       * Add role and privilege class name to current application
       *
       * @param string $roleid
       * @param string $privilegesClassName
       */
      public function addRolePrivileges($roleid, $privilegesClassName) {
          $this->rolePrivileges[$roleid] = $privilegesClassName;
      }
  
      public function getRoleDefaultPrivileges($roleId) {
          if (!array_key_exists($roleId, $this->rolePrivileges)) {
              throw new Gpf_Exception("Privileges not registered for role $roleId. Please register in class " . get_class($this) . " privileges in method registerRolePrivileges by calling method addRolePrivileges");
          }
  
          $className = $this->rolePrivileges[$roleId];
          $objPrivileges = new $className;
          return $objPrivileges->getDefaultPrivileges();
      }
  
      /**
       * Return default privileges by role type
       *
       * @param string $roleType
       * @return Gpf_Privileges
       */
      public function getDefaultPrivilegesByRoleType($roleType) {
          foreach ($this->rolePrivileges as $roleid => $className) {
              $objRole = new Gpf_Db_Role();
              $objRole->setId($roleid);
              $objRole->load();
              if ($objRole->getRoleType() == $roleType) {
                  return new $className;
              }
          }
          return false;
      }
  
  
      public function getName() {
          return $this->_('Application Name');
      }
  
      abstract public function getAuthClass();
  
      /**
       * @return Gpf_Db_Account
       */
      abstract public function createAccount();
  
      /**
       * @return Gpf_Plugins_Definition
       */
      public function getApplicationPluginsDefinition() {
          return array(new Gpf_Definition());
      }
  
      public function getFeaturePathsDefinition() {
          return array();
      }
  
      public function initDatabase() {
      }
  
      protected function importPrivileges($roleId, $privilegeList) {
          foreach ($privilegeList as $object => $privileges) {
              foreach ($privileges as $privilege) {
                  $rolePrivilege = new Gpf_Db_RolePrivilege();
                  $rolePrivilege->setRoleId($roleId);
                  $rolePrivilege->setObject($object);
                  $rolePrivilege->setPrivilege($privilege);
                  $rolePrivilege->insert();
              }
          }
  
      }
  
      public static function isDemo() {
          return Gpf::YES == Gpf_Settings::get(Gpf_Settings_Gpf::DEMO_MODE);
      }
  
      public static function isDemoEntryId($id) {
          return substr($id, 0, 4) == "1111";
      }
  }

} //end Gpf_Application

if (!class_exists('Pap_Application', false)) {
  class Pap_Application extends Gpf_Application {
      const ROLETYPE_MERCHANT = 'M';
      const ROLETYPE_AFFILIATE = 'A';
  
      const DEFAULT_ROLE_MERCHANT = 'pap_merc';
      const DEFAULT_ROLE_AFFILIATE = 'pap_aff';
  
      public function getAuthClass() {
          return 'Pap_AuthUser';
      }
  
      public function getDefaultLanguage() {
          return Pap_Branding::DEFAULT_LANGUAGE_CODE;
      }
  
  
      public function initDatabase() {
          $role = new Gpf_Db_Role();
          $role->setId(self::DEFAULT_ROLE_MERCHANT);
          $role->setName('Merchant');
          $role->setRoleType(self::ROLETYPE_MERCHANT);
          $role->insert();
  
          $role = new Gpf_Db_Role();
          $role->setId(self::DEFAULT_ROLE_AFFILIATE);
          $role->setName('Affiliate');
          $role->setRoleType(self::ROLETYPE_AFFILIATE);
          $role->insert();
      }
  
      public function registerRolePrivileges() {
          $this->addRolePrivileges(self::DEFAULT_ROLE_MERCHANT, 'Pap_Privileges_Merchant');
          $this->addRolePrivileges(self::DEFAULT_ROLE_AFFILIATE, 'Pap_Privileges_Affiliate');
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Application.registerRolePrivileges', $this);
      }
  
      public function createSettings($onlyFile = false) {
          return new Pap_Settings($onlyFile);
      }
  
  	protected function addSmartyPluginsDir() {
  		parent::addSmartyPluginsDir();
      	Gpf_Paths::getInstance()->addSmartyPluginPath(Gpf_Paths::getInstance()->getTopPath() . 'include/Pap/SmartyPlugins');
      }
  
      public function getVersion() {
          return '4.5.74.7';
      }
  
      public function getHelpUrl() {
      	if ($this->isInstalled()) {
      		return Gpf_Settings::get(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK);
      	}
          return parent::getHelpUrl();
      }
  
      protected function computeLatestInstalledApplicationVersion() {
          return Gpf_Db_Table_Versions::getInstance()->getLatestVersion(array($this->getCode(), 'paplite'));
      }
  
      public function getCode() {
          return 'pap';
      }
  
      public function getName() {
          return Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
      }
  
      /**
       * @return Pap_Account
       */
      public function createAccount() {
          return new Pap_Account();
      }
  
      public function getApiFileName() {
      	return 'PapApi.class.php';
      }
  
      /**
       * @return Gpf_Plugins_Definition
       */
      public function getApplicationPluginsDefinition() {
          $plugins = parent::getApplicationPluginsDefinition();
          $plugins[] = new Pap_Definition();
          return $plugins;
      }
  
      public function getFeaturePathsDefinition() {
          return array_merge(parent::getFeaturePathsDefinition(),
                             array(Gpf_Paths::getInstance()->getTopPath().'include/Pap/Features/'));
      }
  
      protected function initLogger() {
      	try {
          	Gpf_Log::addLogger(Gpf_Log_LoggerDatabase::TYPE, Pap_Logger::getLogLevel());
          } catch (Gpf_Exception $e) {
          }
      }
  }

} //end Pap_Application

if (!class_exists('Gpf_Plugins_Engine', false)) {
  class Gpf_Plugins_Engine extends Gpf_Object {
  
      const PROCESS_CONTINUE = 'C';
      const PROCESS_STOP_EXTENSION_POINT = 'S';
      const PROCESS_STOP_ALL = 'A';
      const PROCESS_STOP_EXIT = 'E';
  
      /**
       * @var Gpf_Plugins_Engine
       */
      protected static $instance = null;
  
      /**
       * @var Gpf_Plugins_EngineSettings
       */
      private $configuration;
      /**
       * @var array of Gpf_Plugins_Definition
       */
      protected $availablePlugins;
  
      /**
       * constructs plugin engine instance
       * It loads config data from plugins_config.php and initializes the responsible plugins
       *
       */
      protected function __construct() {
          if (Gpf_Paths::getInstance()->isMissingAccountDirectory()) {
              $this->configuration = $this->generateConfiguration();
              return;
          }
          $config = new Gpf_Plugins_EngineConfigFile();
          try {
              $this->configuration = $config->loadConfiguration();
              return;
          } catch (Exception $e) {
              Gpf_Log::info($this->_('Engine config is not exists: %s', $e->getMessage()));
          }        
          $config->createEmpty();
          $this->configuration = $this->generateConfiguration();
          try {
              $config = new Gpf_Plugins_EngineConfigFile();
              $config->saveConfiguration($this->configuration);
          } catch (Exception $e) {
              Gpf_Log::error($this->_('Unable to save engine config file! %s', $e->getMessage()));
              throw $e;
          }
      }
  
      /**
       * returns actual plugin engine configuration loaded from the file
       *
       * @return Gpf_Plugins_EngineSettings
       */
      public function getConfiguration() {
          return $this->configuration;
      }
  
      /**
       * returns instance of plugins Engine class
       *
       * @return Gpf_Plugins_Engine
       */
      public static function getInstance() {
          if (self::$instance == null) {
              self::$instance = new Gpf_Plugins_Engine();
          }
          return self::$instance;
      }
  
      /**
       * @throws Gpf_Exception
       * returns array of plugins objects for all available plugins
       *
       * @return array of Gpf_Plugins_Definition
       */
      public function getAvailablePlugins() {
          if($this->availablePlugins === null) {
              $this->availablePlugins = array();
              $this->computeApplicationPlugins();
              $this->computeAvailableFeaturePlugins();
              $this->computeAvailablePlugins();
              $this->checkPluginsUnique();
          }
          return $this->availablePlugins;
      }
  
      /**
       * @throws Gpf_Exception
       */
      protected function checkPluginsUnique() {
          $plugins = array();
          foreach ($this->availablePlugins as $plugin) {
              if (in_array($plugin->getCodeName(), $plugins)) {
                  throw new Gpf_Exception($this->_("Too many plugins with code name '%s'", $plugin->getCodeName()));
              }
              $plugins[] = $plugin->getCodeName();
          }
      }
  
      private function computeApplicationPlugins() {
          $this->availablePlugins = array_merge($this->availablePlugins, Gpf_Application::getInstance()->getApplicationPluginsDefinition());
      }
  
      private function computeAvailableFeaturePlugins() {
          if (defined('ENABLE_ENGINECONFIG_LOG')) {
              Gpf_Log::info('computeAvailableFeaturePlugins - path:' . print_r(Gpf_Application::getInstance()->getFeaturePathsDefinition(), true));
          }
          $this->addPluginsFromPath(Gpf_Application::getInstance()->getFeaturePathsDefinition());
      }
  
      private function computeAvailablePlugins() {
          $this->addPluginsFromPath(Gpf_Paths::getInstance()->getPluginsPaths());
      }
  
      private function addPluginsFromPath($pluginDirectoriesPaths) {
          foreach($pluginDirectoriesPaths as $pluginDirectoryPath) {
              $iterator = new Gpf_Io_DirectoryIterator($pluginDirectoryPath, '', false, true);
              foreach ($iterator as $fullPath => $pluginName) {
                  if (defined('ENABLE_ENGINECONFIG_LOG')) {
                      Gpf_Log::info('addPluginsFromPath - path:' . $pluginDirectoriesPaths . ', fullpath: ' . $fullPath . ', pluginName: ' . $pluginName);
                  }
                  try {
                      $this->availablePlugins[] = $this->createPlugin($fullPath);
                  } catch(Gpf_Exception $e) {
                      if (defined('ENABLE_ENGINECONFIG_LOG')) {
                          Gpf_Log::error('error during loading plkugin from directory: ' . $e->getMessage());
                      }
                  }
              }
          }
      }
  
      /**
       *
       * @param unknown_type $path
       * @return Gpf_Plugins_Definition
       */
      private function createPlugin($path) {
          $className = '';
          while (basename($path) != rtrim(Gpf_Paths::PLUGINS_DIR, '/') && basename($path) != 'include') {
              $className =  basename($path) . '_' . $className;
              $path = dirname($path);
          }
          $className .= 'Definition';
          if (Gpf::existsClass($className) === false) {
              throw new Gpf_Exception("Plugin definition class is missing in directory '$path'");
          }
          return new $className;
      }
  
  
      /**
       * Executes given extension point, which means it will run
       * all its registered handlers.
       *
       * @param string $extensionPointName
       * @param object $context
       */
      public static function extensionPoint($extensionPointName, $context = null) {
          $pluginsEngine = self::getInstance();
          try {
              $definition = $pluginsEngine->getDefinitionForExtensionPoint($extensionPointName);
              $extensionPoint = Gpf_Plugins_ExtensionPoint::getInstance($extensionPointName, $definition);
          } catch(Gpf_Exception $e) {
              Gpf_Log::warning("Extension point $extensionPointName not defined (" . $e->getMessage() . ")", "plugins");
              return;
          }
  
          $extensionPoint->processHandlers($context);
      }
  
      /**
       * reads definition of this extension point (context & handlers) from engine configuration
       *
       * @param string $extensionPointName
       * @return array
       */
      private function getDefinitionForExtensionPoint($extensionPointName) {
          if($this->configuration === null) {
              throw new Gpf_Plugins_Exception("Plugins engine is not configured!");
          }
  
          $extPoints = $this->configuration->getExtensionPoints();
           
          if(!is_array($extPoints) || count($extPoints) == 0) {
              throw new Gpf_Plugins_Exception("Plugins engine extension points are not configured!");
          }
           
          if(!isset($extPoints[$extensionPointName])) {
              throw new Gpf_Plugins_Exception("Extension point '$extensionPointName' is not defined");
          }
           
          return $extPoints[$extensionPointName];
      }
      /**
       * Function generates configuration for the given active plugins.
       * It also checks if the configuration is correct, if the plugins given
       * really exist, etc.
       * Throws exception on error
       *
       * @param array $activePluginsCodes
       * @return Gpf_Plugins_EngineSettings
       */
      private function generateConfiguration($activePluginsCodes = array()) {
          $allPlugins = $this->getAvailablePlugins();
  
          $activePluginsObjects = array();
  
          //add system plugins
          foreach($allPlugins as $plugin) {
              if ($plugin->isSystemPlugin()) {
                  $activePluginsObjects[] = $plugin;
              }
          }
  
          if (defined('ENABLE_ENGINECONFIG_LOG')) {
              Gpf_Log::info('generateConfiguration - activating: ' . print_r($activePluginsCodes, true));
          }
  
          //add other active plugins
          foreach($activePluginsCodes as $activePluginCode) {
              $activePlugin = $this->findPlugin($activePluginCode);
              if($activePlugin === null) {
                  if (defined('ENABLE_ENGINECONFIG_LOG')) {
                      Gpf_Log::info('plugin is null for code: ' . $activePluginCode);
                  }
                  continue;
              }
              if (!$activePlugin->isSystemPlugin()) {
                  $activePluginsObjects[] = $activePlugin;
              }
          }
          if (defined('ENABLE_ENGINECONFIG_LOG')) {
              Gpf_Log::info('generateConfiguration - active plugin objects: ' . print_r($activePluginsObjects, true));
          }
          $configuration = new Gpf_Plugins_EngineSettings();
          $configuration->init($activePluginsObjects);
          if (defined('ENABLE_ENGINECONFIG_LOG')) {
              Gpf_Log::info('generateConfiguration - serialised configuration: ' . print_r($configuration, true));
          }
          return $configuration;
      }
  
      /**
       * Find plugin by code name in array of plugins
       *
       * @param string $codeName
       * @return Gpf_Plugins_Definition
       */
      public function findPlugin($codeName) {
          if (defined('ENABLE_ENGINECONFIG_LOG')) {
              Gpf_Log::info('findPlugin - ' . print_r($this->getAvailablePlugins(), true));
          }
          foreach($this->getAvailablePlugins() as $plugin) {
              if($codeName == $plugin->getCodeName()) {
                  return $plugin;
              }
          }
          return null;
      }
  
      /**
       * Function will activate or deactivate given plugin
       *
       * @param string $code
       * @param boolean $activate - if to activate or deactivate
       * @return boolean true/false
       */
      public function activate($codeName, $activate) {
          $plugin = $this->findPlugin($codeName);
          if ($plugin === null) {
              throw new Gpf_Exception($this->_('Plugin %s not found', $codeName));
          }
          $this->activatePlugin($plugin, $activate);
          return true;
      }
  
      public function saveConfiguration(){
          $config = new Gpf_Plugins_EngineConfigFile();
          $config->saveConfiguration( $this->configuration);
      }
  
      public function refreshConfiguration() {
          $config = new Gpf_Plugins_EngineConfigFile();
          $config->saveConfiguration($this->generateConfiguration($this->configuration->getActivePlugins()));
      }
  
      /**
       *  Configuration is not saved
       */
      public function clearConfiguration() {
          $this->configuration = $this->generateConfiguration();
          Gpf_Plugins_ExtensionPoint::clear();
      }
  
      /**
       * @param Gpf_Plugins_Definition $plugin
       * @param boolean $activate
       */
      protected function activatePlugin(Gpf_Plugins_Definition $plugin, $activate) {
          if($activate) {
              $plugin->check();
              $plugin->onActivate();
              if (defined('ENABLE_ENGINECONFIG_LOG')) {
                  Gpf_Log::info('Gpf_Plugins_Engine/activatePlugin: ' . $plugin->getName() . ' - activating');
              }
              // add to active plugins array
              $activePluginsCodes = $this->configuration->getActivePlugins();
              if(!in_array($plugin->getCodeName(), $activePluginsCodes)) {
                  $activePluginsCodes[$plugin->getCodeName()] = $plugin->getCodeName();
              }
          } else {
              $plugin->onDeactivate();
              // remove from active plugins array
              if (defined('ENABLE_ENGINECONFIG_LOG')) {
                  Gpf_Log::info('Gpf_Plugins_Engine/activatePlugin: ' . $plugin->getName() . ' - deactivating');
              }
              $activePluginsCodes = $this->configuration->getActivePlugins();
              if(array_key_exists($plugin->getCodeName(), $activePluginsCodes)) {
                  unset($activePluginsCodes[$plugin->getCodeName()]);
              }
          }
          $this->configuration = $this->generateConfiguration($activePluginsCodes);
      }
  }
  

} //end Gpf_Plugins_Engine

if (!class_exists('Gpf_File_Config', false)) {
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
  

} //end Gpf_File_Config

if (!class_exists('Gpf_Plugins_EngineConfigFile', false)) {
  class Gpf_Plugins_EngineConfigFile extends Gpf_File_Config {
      const FILE_NAME = 'engineconfig.php';
      const CONFIGURATION = 'config';
  
      public function __construct() {
          parent::__construct(Gpf_Paths::getInstance()->getRealAccountConfigDirectoryPath(). self::FILE_NAME);
      }
      
      public function createEmpty() {
          $file = new Gpf_Io_File($this->getSettingFileName());
          $file->setFileMode('w');
          $file->setFilePermissions(0777);
          $file->write('');
          $file->close();
      }
      
      /**
       *
       * @return Gpf_Plugins_EngineSettings
       */
      public function loadConfiguration() {
  		$serialized = $this->getSetting(self::CONFIGURATION);
          $configuration = @unserialize($serialized);
          if(!($configuration instanceof Gpf_Plugins_EngineSettings)) {
              throw new Gpf_Exception('Unserialization error');    		
          }
          return $configuration;
      }
  
      public function saveConfiguration(Gpf_Plugins_EngineSettings $configuration) {
      	if (defined('ENABLE_ENGINECONFIG_LOG')) {
      		Gpf_Log::info('Writing configuration: ' . print_r($configuration, true));
      	}
          $this->setSetting(self::CONFIGURATION, serialize($configuration));
      }
  }
  

} //end Gpf_Plugins_EngineConfigFile

if (!interface_exists('Gpf_Data_Row', false)) {
  interface Gpf_Data_Row {
      public function get($name);
  
      public function set($name, $value);
  }

} //end Gpf_Data_Row

if (!interface_exists('Gpf_Templates_HasAttributes', false)) {
  interface Gpf_Templates_HasAttributes {
      function getAttributes();
  }

} //end Gpf_Templates_HasAttributes

if (!class_exists('Gpf_DbEngine_RowBase', false)) {
  abstract class Gpf_DbEngine_RowBase extends Gpf_Object implements Gpf_Data_Row, Gpf_Templates_HasAttributes {
      /**
       * @var boolean
       */
      protected $isPersistent;
          
      abstract public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '');
      abstract public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select);
      
      /**
       * @return boolean true if object has been loaded from database, otherwise false
       */
      public function isPersistent() {
          return $this->isPersistent;
      }
  
      public function setPersistent($persistent) {
          $this->isPersistent = $persistent;
      }
      
      /**
       * Inserts row
       *
       */
      public function insert() {
          throw new Gpf_Exception('Unimplemented');
      }
      
      /**
       *
       */
      public function update($updateColumns = array()) {
          throw new Gpf_Exception('Unimplemented');
      }
      
      /**
       *
       */
      public function load() {
          throw new Gpf_Exception('Unimplemented');
      }
  }
  

} //end Gpf_DbEngine_RowBase

if (!interface_exists('Gpf_Rpc_Serializable', false)) {
  interface Gpf_Rpc_Serializable {
  
      public function toObject();
  
      public function toText();
  }

} //end Gpf_Rpc_Serializable

if (!class_exists('Gpf_DbEngine_Row', false)) {
  class Gpf_DbEngine_Row extends Gpf_DbEngine_RowBase implements Iterator, Gpf_Rpc_Serializable, Gpf_Templates_HasAttributes  {
      const NULL = '_NULL_';
  
      /**
       * @var array
       */
      private $columns;
      /**
       * @var Gpf_DbEngine_Table
       */
      private $table;
  
      /**
       * @var Gpf_DbEngine_Database
       */
      private $db;
  
  
      /**
       * @var boolean
       */
      private $recordChanged = true;
  
      /**
       * iterator position
       *
       * @var int
       */
      private $position = 0;
  
      /**
       * @var array of Gpf_DbEngine_Row_Constraint
       */
      private $constraints = array();
  
      private $tableColumns;
  
      /**
       * Creates instance of Db_Row object and generates new primary key value
       */
      public function __construct() {
          $this->db = $this->createDatabase();
          $this->init();
      }
  
      /**
       * @return string text representation of Db_Row object
       */
      public function __toString() {
          return get_class($this) . " (" . $this->toText() . ')';
      }
  
      /**
       * Return array of attributes in form column -> value
       *
       * @return array
       */
      public function toArray() {
          $array = array();
          foreach ($this as $key => $value) {
              $array[$key] = $value;
          }
          return $array;
      }
  
      /**
       * Deletes row. Primary key value must be set before this function is called
       */
      public function delete() {
          if($this->isPrimaryKeyEmpty()) {
              throw new Gpf_Exception("Could not delete Row. Primary key values are empty");
          }
  
          foreach ($this->table->getDeleteConstraints() as $deleteConstraint) {
              $deleteConstraint->execute($this);
          }
  
          $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
          $deleteBuilder->from->add($this->table->name());
          $deleteBuilder->where = $this->getPrimaryWhereClause();
           
          $deleteBuilder->deleteOne();
      }
  
      /**
       * Updates row. Primary key value must be set before this function is called
       *
       * @param array $updateColumns list of columns that should be updated. if not set, all modified columns are update
       * @throws Gpf_DbEngine_Row_ConstraintException
       */
      public function update($updateColumns = array()) {
          if($this->isPrimaryKeyEmpty()) {
              throw new Gpf_Exception("Could not update Row. Primary key values are empty");
          }
  
          $this->beforeSaveCheck();
  
          $this->beforeSaveAction();
  
          $this->updateRow($updateColumns);
      }
  
      /**
       * Inserts row
       *
       * @throws Gpf_DbEngine_Row_ConstraintException
       * @throws Gpf_DbEngine_DuplicateEntryException
       */
      public function insert() {
          $this->beforeSaveCheck();
  
          $this->beforeSaveAction();
  
          $this->insertRow();
      }
  
      /**
       * Saves row. If row exists in table (was loaded before) it is updated,
       * otherwise new row is added
       *
       * @throws Gpf_DbEngine_Row_ConstraintException
       * @throws Gpf_DbEngine_DuplicateEntryException
       */
      public function save() { 	
          if ($this->isPersistent()) {
              if ($this->isChanged()) {
                  $this->update();
              }
          } else {
              $this->insert();
          }
      }
  
      /**
       * Loads row by primary key value
       *
       * @throws Gpf_DbEngine_NoRowException if selected row does not exist
       */
      public function load() {
          $this->loadRow($this->getPrimaryColumns());
      }
  
      /**
       * Loads row by attribute values that have been already set
       * If $loadColumns parameter is set, row is loaded by values in columns specified by $loadColumns parameter
       *
       * @param array $loadColumns list of column names
       * @throws Gpf_DbEngine_NoRowException
       * @throws Gpf_DbEngine_TooManyRowsException
       */
      public function loadFromData(array $loadColumns = array()) {
          $this->loadRow($this->getLoadKey($loadColumns), true);
      }
  
      /**
       * Loads collection of row objects by attribute values that have been already set
       * If $loadColumns parameter is set, collection is loaded by values in columns specified by $loadColumns parameter
       *
       * @param array $loadColumns
       * @return Gpf_DbEngine_Row_Collection
       */
      public function loadCollection(array $loadColumns = array()) {
          $select = $this->getLoadSelect($this->getLoadKey($loadColumns), true);
          return $this->loadCollectionFromRecordset($select->getAllRows());
      }
  
      /**
       * @param $rowsRecordSet
       * @return Gpf_DbEngine_Row_Collection
       */
      public function loadCollectionFromRecordset(Gpf_Data_RecordSet $rowsRecordSet) {
          return $this->fillCollectionFromRecordset(new Gpf_DbEngine_Row_Collection(), $rowsRecordSet);
      }
  
      /**
       * @return Gpf_DbEngine_Row_Collection
       */
      protected function fillCollectionFromRecordset(Gpf_DbEngine_Row_Collection $collection, Gpf_Data_RecordSet $rowsRecordSet) {
          foreach ($rowsRecordSet as $rowRecord) {
              $dbRow = clone $this;
              $dbRow->fillFromRecord($rowRecord);
              $dbRow->isPersistent = true;
              $collection->add($dbRow);
          }
          return $collection;
      }
  
      /**
       * Checks if row with primary key already exists
       *
       * @return true if row exists, otherwise false
       */
      public function rowExists() {
          try {
              $select = $this->getLoadSelect($this->getPrimaryColumns());
              $select->getOneRow();
          } catch (Gpf_Exception $e) {
              return false;
          }
          return true;
      }
  
      /**
       * Fills Db_Row from a record
       * Fields that are not part of the Db_Row are ignored
       *
       * @param Gpf_Data_Record $record
       */
      public function fillFromRecord(Gpf_Data_Record $record) {
          foreach ($this->tableColumns as $column) {
              $name = $column->name;
              try {
                  $this->set($name, $record->get($name));
              } catch (Gpf_Exception $e) {
              }
          }
          $this->afterLoad();
      }
  
      /**
       * Fills Db_Row from select. Select should return one row.
       *
       * @param Gpf_SqlBuilder_SelectBuilder $select
       * @throws Gpf_DbEngine_NoRowException
       * @throws Gpf_DbEngine_TooManyRowsException
       */
      public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select) {
          $this->fillFromRecord($select->getOneRow());
          $this->isPersistent = true;
      }
  
      /**
       * Sets value of the primary key
       *
       * @param string $value
       * @throws Gpf_Exception if row has more than a one primary key
       */
      public function setPrimaryKeyValue($value) {
          $this->set($this->getSinglePrimaryKeyColumn()->getName(), $value);
      }
  
      /**
       * Gets value of the primary key
       *
       * @throws Gpf_Exception if row has more than a one primary key
       * @return string
       */
      public function getPrimaryKeyValue() {
          return $this->get($this->getSinglePrimaryKeyColumn()->getName());
      }
  
      /**
       * Performs explicit check on Db_Row
       *
       * @throws Gpf_DbEngine_Row_CheckException if there is some error
       */
      public function check() {
          $constraintExceptions = array();
  
          foreach ($this->table->getConstraints() as $constraint) {
              try {
                  $constraint->validate($this);
              } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                  $constraintExceptions[] = $e;
              }
          }
          if (count($constraintExceptions) > 0) {
              throw new Gpf_DbEngine_Row_CheckException($constraintExceptions);
          }
      }
  
      /**
       * Sets value of the field to SQL NULL
       *
       * @param string $name
       * @throws Gpf_DbEngine_Row_MissingFieldException
       */
      public function setNull($name) {
          $this->set($name, self::NULL);
      }
  
      public function isPrimaryKeyEmpty() {
          return $this->isRowKeyEmpty($this->getPrimaryColumns());
      }
  
      /**
       *
       * @return array
       */
      public function getPrimaryColumns() {
          return $this->table->getPrimaryColumns();
      }
  
      public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '') {
          $alias = rtrim($aliasPrefix, '_');
          foreach($this->tableColumns as $column) {
              if($aliasPrefix != '') {
                  $select->select->add($column->name, $aliasPrefix . $column->name, $alias);
              } else {
                  $select->select->add($column->name);
              }
          }
      }
  
      /**
       * @return Gpf_DbEngine_Table
       */
      public function getTable() {
          return $this->table;
      }
  
      /*************************************************************************/
      /********************** Interface: Gpf_Data_Row ************************/
      /*************************************************************************/
  
      /**
       * Sets value of the field
       *
       * @param string $name
       * @param mixed $value
       * @throws Gpf_DbEngine_Row_MissingFieldException
       */
      public function set($name, $value) {
          if (is_object($value)) {
              throw new Gpf_Exception("Value of column $name cannot be an object");
          }
          $value = (string) $value;
          if($this->get($name) === $value) {
              return;
          }
          $this->recordChanged = true;
  
          if ($value === '' && in_array($this->tableColumns[$name]->getType(),
          array(Gpf_DbEngine_Column::TYPE_NUMBER, Gpf_DbEngine_Column::TYPE_DATE))) {
              $this->setNull($name);
          } else {
              $this->columns[$name] = $value;
          }
      }
  
      public function setChanged($value) {
          $this->recordChanged = $value;
      }
  
      /**
       * Returns value of the field
       *
       * @param string $name name of the field
       * @return string
       * @throws Gpf_DbEngine_Row_MissingFieldException
       */
      public function get($name) {
          $value = $this->getInternalValue($name);
          if ($value == self::NULL) {
              return null;
          }
          return $value;
      }
  
      /*************************************************************************/
      /******************* Interface: Gpf_Rpc_Serializable ***********************/
      /*************************************************************************/
  
      public function toObject() {
          $obj = new stdClass();
          foreach ($this as $id => $val) {
              $obj->$id = $val;
          }
          return $obj;
      }
  
      public function toText() {
          $text = "";
          foreach ($this as $id => $value) {
              $text .= "$id = $value, ";
          }
          return rtrim($text, ", ");
      }
  
      /*************************************************************************/
      /************* Interface: Gpf_Templates_HasAttributes ******************/
      /*************************************************************************/
       
      public function getAttributes() {
          return $this->toArray();
      }
  
      /*************************************************************************/
      /************************* Interface: Iterator ***************************/
      /*************************************************************************/
       
  
      public function current() {
          $columns = $this->tableColumns;
          return $this->get($this->key());
      }
  
      public function key() {
          $columns = $this->tableColumns;
          $i=0;
          foreach ($columns as $id => $column) {
              if ($this->position == $i) {
                  return $id;
              }
              $i++;
          }
          return false;
      }
  
      public function next() {
          $this->position++;
      }
  
      public function rewind() {
          $this->position = 0;
      }
  
      public function valid() {
          return $this->position < count($this->tableColumns);
      }
  
      /**
       * Sets table of the Db_Row object
       *
       * @param Gpf_DbEngine_Table $table
       */
      protected function setTable(Gpf_DbEngine_Table $table) {
          $this->table = $table;
          $this->tableColumns = $table->getColumns();
      }
  
      /**
       * Inits Db_Row object
       *
       */
      protected function init() {
          $this->columns = array();
          $this->isPersistent = false;
      }
  
      /**
       * Generates new primary key value
       * Keys with already set values, don't change
       */
      protected function generatePrimaryKey() {
          foreach($this->table->getPrimaryColumns() as $column) {
              if($column->isAutogenerated() && $column->type == "String" && !strlen($this->get($column->name))) {
                  $this->set($column->name, Gpf_Common_String::generateId($column->length));
              }
          }
      }
  
      /**
       * This method is executed after row object is loaded from database
       */
      protected function afterLoad() {
      }
  
      /**
       * Performs any additional actions that are needed before row is saved
       */
      protected function beforeSaveAction() {
      }
  
      /**
       * Performs check before row is saved
       *
       * @throws Gpf_DbEngine_Row_ConstraintException
       */
      protected function beforeSaveCheck() {
          foreach ($this->table->getConstraints() as $constraint) {
              $constraint->validate($this);
          }
      }
  
      /**
       * @param string $name name of the field
       * @return string, null, self::NULL
       *   - null is returned when value for this field has not been set so far
       *   - self::NULL is returned when value of this field has to be set to null in DB
       * @throws Gpf_DbEngine_Row_MissingFieldException
       */
      private function getInternalValue($name) {
          if (@$this->tableColumns[$name] === null) {
              throw new Gpf_DbEngine_Row_MissingFieldException($name, get_class($this));
          }
          return @$this->columns[$name];
      }
  
      private function getPrimaryWhereClause() {
          return $this->getRowKeyWhereClause($this->getPrimaryColumns());
      }
  
      private function clearPrimaryKey() {
          $primaryKeyColumns = $this->getPrimaryColumns();
          foreach ($primaryKeyColumns as $column) {
              $this->set($column->getName(), null);
          }
      }
  
      private function getLoadKey(array $loadColumns = array()) {
          $rowKey = array();
          if (is_array($loadColumns) && count($loadColumns)) {
              foreach ($loadColumns as $columnName) {
                  $rowKey[] = $this->table->getColumn($columnName);
              }
          } else {
              foreach ($this->tableColumns as $index => $column) {
                  if($this->getInternalValue($column->name) !== null) {
                      $rowKey[$column->name] = $column;
                  }
              }
          }
          return $rowKey;
      }
  
      protected function getRowKeyWhereClause($rowKey) {
          $builder = new Gpf_SqlBuilder_SelectBuilder();
          foreach($rowKey as $column) {
              if($this->getInternalValue($column->name) == self::NULL) {
                  $builder->where->add($column->name, 'is', 'NULL', 'AND', false);
              } else {
                  $builder->where->add($column->name, '=', $this->get($column->name));
              }
          }
          return $builder->where;
      }
  
  
      /**
       * @throws Gpf_DbEngine_NoRowException
       * @throws Gpf_DbEngine_TooManyRowsException
       * @throws Gpf_Exception
       */
      private function loadRow($rowKey, $withAlternate = false) {
          $select = $this->getLoadSelect($rowKey, $withAlternate);
          $this->fillFromSelect($select);
          $this->recordChanged = false;
      }
  
      private function isRowKeyEmpty($rowKey) {
          foreach($rowKey as $column) {
              if($this->get($column->name) === null || $this->get($column->name) == "") {
                  return true;
              }
          }
          return false;
      }
  
      /**
       * @return Gpf_DbEngine_Column
       * @throws Gpf_Exception if row has more than a one primary key
       */
      private function getSinglePrimaryKeyColumn() {
          $primaryKeys = $this->getPrimaryColumns();
          if (count($primaryKeys) != 1) {
              throw new Gpf_Exception("Can not use setPrimaryKeyValue() method as "
              . get_class($this) . " has multiple column primary key");
          }
          reset($primaryKeys);
          return current($primaryKeys);
      }
  
      private function isChanged() {
          return $this->recordChanged;
      }
  
      private function hasAutoIncrementedKey() {
          return $this->table->hasAutoIncrementedKey();
      }
  
      /**
       *
       * @return Gpf_DbEngine_Column
       */
      private function getAutoIncrementedColumn() {
          return $this->table->getAutoIncrementedColumn();
      }
  
      private function hasAutogeneratedKey() {
          foreach($this->table->getPrimaryColumns() as $column) {
              if($column->isAutogenerated() && $column->type == Gpf_DbEngine_Column::TYPE_STRING) {
                  return true;
              }
          }
          return false;
      }
  
      /**
       * @throws Gpf_Exception
       * @return Gpf_SqlBuilder_SelectBuilder
       */
      protected function getLoadSelect($rowKey, $withAlternate = false) {
          if(!$withAlternate && $this->isRowKeyEmpty($rowKey)) {
              throw new Gpf_Exception("Could not load Row. Primary key values empty");
          }
  
          $select = $this->prepareLoadSelect();
          $select->where = $this->getRowKeyWhereClause($rowKey);
          return $select;
      }
  
      private $loadSelect = null;
  
      private function prepareLoadSelect() {
          if ($this->loadSelect === null) {
              $this->loadSelect = new Gpf_SqlBuilder_SelectBuilder();
              $this->prepareSelectClause($this->loadSelect);
              $this->loadSelect->from->add($this->table->name());
              return $this->loadSelect;
          }
          return clone $this->loadSelect;
      }
  
      /**
       * @return Gpf_SqlBuilder_UpdateBuilder
       */
      protected function createUpdateBuilder() {
          return new Gpf_SqlBuilder_UpdateBuilder();
      }
  
      private function updateRow($updateColumns = array()) {
          $updateBuilder = $this->createUpdateBuilder();
          $updateBuilder->from->add($this->table->name());
  
          foreach($this->tableColumns as $column) {
              if(count($updateColumns) > 0 && !in_array($column->name, $updateColumns, true)) {
                  continue;
              }
              $columnValue = $this->getInternalValue($column->name);
              if(!$this->table->isPrimary($column->name) &&  $columnValue !== null) {
                  if($columnValue == self::NULL) {
                      $updateBuilder->set->add($column->name, 'NULL', false);
                  } else {
                      $updateBuilder->set->add($column->name, $columnValue, $column->doQuote());
                  }
              }
          }
  
          $updateBuilder->where = $this->getPrimaryWhereClause();
          
          $updateBuilder->updateOne();
      }
      
      /**
       * @throws Gpf_DbEngine_DuplicateEntryException
       */
      private function insertRow() {
          if ($this->isPrimaryKeyEmpty()) {
              $this->generatePrimaryKey();
          }
  
          $this->executeInsertRow();
          $this->isPersistent = true;
      }
  
      /**
       * @return Gpf_SqlBuilder_InsertBuilder()
       */
      protected function createInsertBuilder() {
          return new Gpf_SqlBuilder_InsertBuilder();
      }
  
      /**
       * @throws Gpf_DbEngine_DuplicateEntryException
       */
      private function executeInsertRow() {
          $insertBuilder = $this->createInsertBuilder();
          $insertBuilder->setTable($this->table);
           
          if($this->hasAutoIncrementedKey() && !$this->get($this->getAutoIncrementedColumn()->getName())) {
              $this->set($this->getAutoIncrementedColumn()->getName(), 0);
          }
          foreach($this->tableColumns as $column) {
              $value = $this->getInternalValue($column->name);
              if ($value === null) {
                  continue;
              }
              if ($value == self::NULL) {
                  $insertBuilder->add($column->name, 'NULL', false);
                  continue;
              }
              $insertBuilder->add($column->name, $value, $column->doQuote());
          }
          if($this->hasAutoIncrementedKey() && !$this->get($this->getAutoIncrementedColumn()->getName())) {
              $statement = $insertBuilder->insertAutoincrement();
              $this->set($this->getAutoIncrementedColumn()->getName(), $statement->getAutoIncrementId());
          } else {
              $insertBuilder->insert();
          }
      }
  }
  

} //end Gpf_DbEngine_Row

if (!class_exists('Gpf_Db_Account', false)) {
  abstract class Gpf_Db_Account extends Gpf_DbEngine_Row {
      const DEFAULT_ACCOUNT_ID = 'default1';
      const APPROVED = 'A';
      const PENDING = 'P';
      const SUSPENDED = 'S';
      const DECLINED = 'D';
  
      private $password;
      private $firstname;
      private $lastname;
  
      function __construct(){
          parent::__construct();
          $this->setApplication(Gpf_Application::getInstance()->getCode());
          $date = new Gpf_DateTime();
          $this->setDateinserted($date->toDateTime());
      }
  
      function init() {
          $this->setTable(Gpf_Db_Table_Accounts::getInstance());
          parent::init();
      }
  
      function setId($id) {
          $this->set(Gpf_Db_Table_Accounts::ID, $id);
      }
  
      public function setDefaultId() {
          $this->setId(self::DEFAULT_ACCOUNT_ID);
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_Accounts::ID);
      }
  
      public function setDateinserted($dateInserted) {
          $this->set(Gpf_Db_Table_Accounts::DATEINSERTED, $dateInserted);
      }
  
      public function getDateinserted() {
          return $this->get(Gpf_Db_Table_Accounts::DATEINSERTED);
      }
  
      /**
       *
       * @return Gpf_Install_CreateAccountTask
       */
      public function getCreateTask() {
          $task = new Gpf_Install_CreateAccountTask();
          $task->setAccount($this);
          return $task;
      }
  
      /**
       *
       * @return Gpf_Install_UpdateAccountTask
       */
      public function getUpdateTask() {
          $task = new Gpf_Install_UpdateAccountTask();
          $task->setAccount($this);
          return $task;
      }
  
      public function createTestAccount($email, $password, $firstName, $lastName) {
          $this->setDefaultId();
          $this->setEmail($email);
          $this->setPassword($password);
          $this->setFirstname($firstName);
          $this->setLastname($lastName);
          $this->getCreateTask()->run(Gpf_Tasks_LongTask::NO_INTERRUPT);
      }
  
      public function getEmail() {
          return $this->get(Gpf_Db_Table_Accounts::EMAIL);
      }
  
      public function getPassword() {
          return $this->password;
      }
  
      public function getStatus() {
          return $this->get(Gpf_Db_Table_Accounts::STATUS);
      }
  
      public function setPassword($password) {
          $this->password = $password;
      }
  
      public function setFirstname($name) {
          $this->firstname = $name;
          $this->setName($this->firstname . ' ' . $this->lastname);
      }
  
      public function setLastname($name) {
          $this->lastname = $name;
          $this->setName($this->firstname . ' ' . $this->lastname);
      }
  
      public function getFirstname() {
          return $this->firstname;
      }
  
      public function getLastname() {
          return $this->lastname;
      }
  
      public function  setName($name) {
          $this->set(Gpf_Db_Table_Accounts::NAME, $name);
      }
  
      public function  setEmail($email) {
          $this->set(Gpf_Db_Table_Accounts::EMAIL, $email);
      }
  
      public function setStatus($newStatus) {
          $this->set(Gpf_Db_Table_Accounts::STATUS, $newStatus);
      }
  
      public function setApplication($application) {
          $this->set(Gpf_Db_Table_Accounts::APPLICATION, $application);
      }
  
      public function getApplication() {
          return $this->get(Gpf_Db_Table_Accounts::APPLICATION);
      }
  
      public function getName() {
          return $this->get(Gpf_Db_Table_Accounts::NAME);
      }
  
      public function setAccountNote($accountNote) {
          $this->set(Gpf_Db_Table_Accounts::ACCOUNT_NOTE, $accountNote);
      }
  
      public function getAccountNote() {
          return $this->get(Gpf_Db_Table_Accounts::ACCOUNT_NOTE);
      }
  
      public function setSystemNote($systemNote) {
          $this->set(Gpf_Db_Table_Accounts::SYSTEM_NOTE, $systemNote);
      }
  
      public function getStystemNote() {
          return $this->get(Gpf_Db_Table_Accounts::SYSTEM_NOTE);
      }
  }

} //end Gpf_Db_Account

if (!interface_exists('Gpf_Common_Stream', false)) {
  interface Gpf_Common_Stream {
      public function getData();
  }
  

} //end Gpf_Common_Stream

if (!class_exists('Gpf_Io_File', false)) {
  class Gpf_Io_File extends Gpf_Object implements Gpf_Common_Stream {
      const BUFFER_SIZE = 4000;
  
      private $textFilesExtensions = array('html','php','tpl','stpl','css','sql','txt','TXT','js');
      private $textFileSpecialNames = array('.htaccess','htaccess');
  
      private $fileName;
      private $extension;
      private $fileMode = 'r';
      private $fileHandler;
      private $isOpened;
      private $filePermissions;
  
      public function __construct($fileName) {
          $this->fileName = $fileName;
          $this->fileHandler = false;
          $this->isOpened = false;
          $this->filePermissions = null;
      }
  
      public function __destruct() {
          $this->close();
      }
  
      public function setFileName($name) {
          $this->fileName = $name;
      }
  
      public function seek($offset) {
          if(-1 == fseek($this->getFileHandler(), $offset)) {
              throw new Gpf_Exception($this->_('Could not seek file', $this->fileName));
          }
      }
  
      public function tell() {
          return ftell($this->getFileHandler());
      }
  
      public function getFileName() {
          return $this->fileName;
      }
  
      /**
       * Set file mode for operations with file
       *
       * @param string $mode possible values are: 'r','r+','w','w+','a','a+','x','x+'
       */
      public function setFileMode($mode) {
          $this->fileMode = $mode;
      }
  
      /**
       * Set file permissions in octal mode.
       *
       * @param int
       */
      public function setFilePermissions($permissions) {
          $this->filePermissions = $permissions;
      }
  
      public function getFileHandler() {
          if($this->fileHandler === false) {
              return $this->open($this->fileMode);
          }
          return $this->fileHandler;
      }
  
      public function open($fileMode = 'r') {
          $this->fileMode = $fileMode;
          $this->fileHandler = null;
          $this->isOpened = false;
          if(!empty($this->fileName)) {
              if(false !== ($this->fileHandler = @fopen($this->fileName, $this->fileMode))) {
                  $this->isOpened = true;
                  return $this->fileHandler;
              }
          }
          //TODO: create Gpf_Io_FileException
          throw new Gpf_Exception($this->_('Could not open file') . ' ' . $this->fileName);
      }
  
      public function lockWrite() {
          return $this->lock(LOCK_EX);
      }
  
      public function lock($operation) {
          if (!$this->isOpened()) {
              throw new Gpf_Exception('Only opened file can be locked');
          }
          for ($i=1; $i<=10; $i++) {
              if (flock($this->fileHandler, $operation)) {
                  return true;
              }
              usleep($i);
          }
          return false;
      }
  
      private function matchPattern($mask){
          $pattern = '/^'.str_replace('/', '\/', str_replace('\*', '.*', preg_quote(trim($mask)))).'/';
          if (@preg_match($pattern, $this->fileName) > 0) {
              return true;
          }
          return false;
      }
  
      public function matchPatterns($filePatterns){
          if (is_array($filePatterns)) {
              foreach($filePatterns as $filePattern){
                  if ($this->matchPattern($filePattern)){
                      return true;
                  }
              }
              return false;
          }
          return $this->matchPattern($filePatterns);
      }
  
      public function close() {
          if($this->isOpened) {
              @fclose($this->fileHandler);
              $this->fileHandler = false;
              $this->isOpened = false;
          }
      }
  
      public function readLine($length = 0) {
          $fileHandler = $this->getFileHandler();
          if($length <= 0) {
              return fgets($fileHandler);
          }
          return fgets($fileHandler, $length);
      }
  
      public function isEof() {
          $fileHandler = $this->getFileHandler();
          return feof($fileHandler);
      }
  
      public function readAsArray() {
          $result = @file($this->fileName);
          if($result === false) {
              throw new Gpf_Exception($this->_('Could not read file') . ' ' . $this->fileName);
          }
          return $result;
      }
  
      public function writeLine($string) {
          $fileHandler = $this->getFileHandler();
          $this->changeFilePermissions();
          return fputs($fileHandler, $string);
      }
  
      public function getSize() {
          return filesize($this->fileName);
      }
  
      /**
       * Get file extension (computes from filename)
       *
       */
      public function getExtension() {
          if (isset($this->extension)) {
              return $this->extension;
          }
          $info = pathinfo($this->getFileName());
          if(isset($info['extension'])) {
              $this->extension = $info['extension'];
          }
          return $this->extension;
      }
  
      public function rewind() {
          $fileHandler = $this->getFileHandler();
          if (!@fseek($fileHandler, 0)) {
              throw new Gpf_Exception($this->_('Rewind unsupported in this file stream'));
          }
      }
  
      public function read($length = 0) {
          $fileHandler = $this->getFileHandler();
          if(true === feof($fileHandler)) {
              return false;
          }
          if($length == 0) {
              $length = $this->getSize();
          }
          return fread($fileHandler, $length);
      }
  
      public function write($string) {
          if(!($fileHandler = $this->getFileHandler())) {
              throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
          }
          $this->changeFilePermissions();
          $result = @fwrite($fileHandler, $string);
          if($result === false || ($result == 0 && strlen($string) != 0)) {
              throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
          }
          return $result;
      }
  
      public function writeCsv($array, $delimiter) {
          if($fileHandler = $this->getFileHandler()) {
              $this->changeFilePermissions();
              $result = @fputcsv($fileHandler, $array, $delimiter);
              if($result === false) {
                  throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
              }
          }
      }
  
      public function readCsv($delimiter) {
          $fileHandler = $this->getFileHandler();
          if(true === feof($fileHandler)) {
              return false;
          }
          return fgetcsv($fileHandler, 0, $delimiter);
      }
  
      public function passthru() {
          $fileHandler = $this->getFileHandler();
          return fpassthru($fileHandler);
      }
  
      public function getContents() {
          if(!$this->isExists()) {
              throw new Gpf_Exception($this->_('File %s does not exist.', $this->fileName));
          }
          if (($content = @file_get_contents($this->fileName)) === false) {
              throw new Gpf_Exception($this->_('Failed to read file %s', $this->fileName));
          }
          return $content;
      }
  
      public function putContents($data) {
          if(!$this->isExists()) {
              throw new Gpf_Exception($this->_('File %s does not exist.', $this->fileName));
          }
          if ($content = file_put_contents($this->fileName, $data) === false) {
              throw new Gpf_Exception($this->_('Failed to write file %s', $this->fileName));
          }
          return true;
      }
  
      public function getCheckSum() {
          if (in_array($this->getFileName(), $this->textFileSpecialNames) || in_array($this->getExtension(), $this->textFilesExtensions)) {
              return md5(str_replace(array("\r\n", "\r"), "\n", $this->getContents()));
          }
          return md5($this->getContents());
      }
  
      /**
       * Checks if selected file exists
       *
       * @return boolean true if file exists, otherwise false
       */
      public function isExists() {
          return self::isFileExists($this->fileName);
      }
  
      public static function isFileExists($fileName) {
          return @file_exists($fileName);
      }
  
      public function isDirectory() {
          return @is_dir($this->fileName);
      }
  
      public function isWritable() {
          return is_writable($this->fileName);
      }
  
      public function emptyFiles($recursive = false, $excludeFiles = null) {
          if ($this->isDirectory()) {
              if ($recursive == true) {
                  $dir = new Gpf_Io_DirectoryIterator($this, '', false, true);
                  foreach ($dir as $fullFileName => $fileName) {
                      $file = new Gpf_Io_file($fullFileName);
                      $file->emptyFiles(true);
                      $file->rmdir();
                  }
              }
              $dir = new Gpf_Io_DirectoryIterator($this, '', false);
              foreach ($dir as $fullFileName => $fileName) {
                  $file = new Gpf_Io_file($fullFileName);
                   
                  if (!is_array($excludeFiles)) {
                      $file->delete();
                  }else{
                      if (!in_array($fileName,$excludeFiles)) {
                          $file->delete();
                      }
                  }
              }
          } else {
              throw new Gpf_Exception($this->_('%s is not directory!', $this->fileName));
          }
          return true;
      }
  
      public function rmdir() {
          if (!@rmdir($this->getFileName())) {
              throw new Gpf_Exception($this->_('Could not delete directory %s', $this->fileName));
          }
      }
  
      /**
       * @throws Gpf_Exception
       */
      public function mkdir($recursive = false, $mode = 0777) {
          $mkMode = $mode;
          if($mkMode === null) {
              $mkMode = 0777;
          }
          if(false === @mkdir($this->fileName, $mkMode, $recursive)) {
              throw new Gpf_Exception($this->_('Could not create directory %s', $this->fileName));
          }
          if($mode !== null) {
              @chmod($this->getFileName(), $mode);
          }
      }
  
      public function recursiveCopy(Gpf_Io_File $target, $mode = null){
          if ($this->isDirectory()) {
              $dir = new Gpf_Io_DirectoryIterator($this, '', false, true);
              foreach ($dir as $fullFileName => $fileName) {
                  $file = new Gpf_Io_File($fullFileName);
                  $targetDir = new Gpf_Io_File($target->getFileName() . '/' . $fileName);
                  $targetDir->mkdir();
                  $file->recursiveCopy($targetDir);
              }
              $dir = new Gpf_Io_DirectoryIterator($this, '', false);
              foreach ($dir as $fullFileName => $fileName) {
                  $srcFile = new Gpf_Io_File($fullFileName);
                  $dstFile = new Gpf_Io_File($target->getFileName() . '/' . $fileName);
  
                  $this->copy($srcFile, $dstFile);
              }
          } else {
              throw new Gpf_Exception($this->_('%s is not directory!', $this->fileName));
          }
          return true;
      }
  
      /**
       * @return Gpf_Io_File
       */
      public function getParent(){
          $slashIndex = strrpos($this, '/');
          if($slashIndex == strlen($this) - 1){
              $slashIndex = strrpos($this, '/', -2);
          }
          return new Gpf_Io_File(substr($this, 0, $slashIndex + 1));
      }
  
      /**
       * @throws Gpf_Exception
       */
      public static function copy(Gpf_Io_File $source, Gpf_Io_File $target, $mode = null) {
          if (Gpf_Php::isFunctionEnabled('copy')) {
              if(false === @copy($source->getFileName(), $target->getFileName())) {
                  throw new Gpf_Exception('Could not copy ' .
                  $source->getFileName() . ' to ' . $target->getFileName());
              }
          } else {
              $target->open('w');
              $target->write($source->getContents());
          }
          if($mode !== null) {
              @chmod($target->getFileName(), $mode);
          }
      }
  
      public function getData() {
          return $this->read(self::BUFFER_SIZE);
      }
  
      public function getInodeChangeTime() {
          clearstatcache();
          return filemtime($this->fileName);
      }
      /**
       * @return boolean
       */
      public function delete() {
          return @unlink($this->getFileName());
      }
  
      public function getFilePermissions() {
          if (function_exists('fileperms')) {
              return substr(sprintf('%o', @fileperms($this->fileName)), -4);
          }
          return 'not supported';
      }
  
      public function getFileOwner() {
          if (function_exists('fileowner')) {
              return @fileowner($this->fileName);
          }
          return 'not supported';
      }
  
      private function changeFilePermissions() {
          if ($this->filePermissions != null) {
              if (!@chmod($this->fileName, $this->filePermissions)) {
                  throw new Gpf_Exception($this->_("Could not change permissions %s", $this->fileName));
              }
              $this->filePermissions = null;
          }
      }
  
      /**
       * Return open status of file
       *
       * @return boolean Returns true if file is opened
       */
      public function isOpened() {
          return $this->isOpened;
      }
  
      /**
       * Outputs file to the output buffer
       */
      public function output() {
          if (@readfile($this->fileName) == null) {
              if (!Gpf_Php::isFunctionEnabled('fpassthru')) {
                  echo file_get_contents($this->fileName);
              } else {
                  $fp = fopen($this->fileName, 'r');
                  fpassthru($fp);
                  fclose($fp);
              }
          }
      }
  
      public function __toString(){
          return $this->getFileName();
      }
  
      public function getName() {
          return basename($this->fileName);
      }
  
      public function getMimeType() {
          return Gpf_Io_MimeTypes::getMimeType($this->getExtension());
      }
  }
  

} //end Gpf_Io_File

if (!class_exists('Gpf_Plugins_EngineSettings', false)) {
  class Gpf_Plugins_EngineSettings extends Gpf_Object {
      public $activePlugins = array();
      public $extensionPoints = array();
  
      public function __construct() {
      }
  
      public function getActivePlugins() {
          return $this->activePlugins;
      }
  
      public function isPluginActive($codename) {
          return in_array($codename, $this->activePlugins);
      }
  
      public function getExtensionPoints() {
          return $this->extensionPoints;
      }
  
      public function init(array $plugins) {
  
          $arrDefines = array();
          $arrImplements = array();
  
          foreach($plugins as $plugin) {
              $this->activePlugins[$plugin->getCodeName()] = $plugin->getCodeName();
  
              $arrDefines = $this->mergeDefines($arrDefines, $plugin->getDefines());
              $arrImplements = array_merge($arrImplements, $plugin->getImplements());
          }
  
          $this->extensionPoints = $this->generateExtensionPoints($arrDefines, $arrImplements);
      }
  
      private function mergeDefines($arr1, $arr2) {
          $arrMerged = $arr1;
  
          foreach($arr2 as $define) {
              if($this->checkExtensionPointExistsInArray($define->getExtensionPoint(), $arr1)) {
                  throw new Gpf_Exception("Extension point '".$define->getExtensionPoint()."' was already defined by another plugin, they cannot have duplicated names!");
              }
              $arrMerged[] = $define;
          }
  
          return $arrMerged;
      }
  
      private function checkExtensionPointExistsInArray($extensionPointName, $arr) {
          if(count($arr) == 0) {
              return false;
          }
  
          foreach($arr as $define) {
              if($define->getExtensionPoint() == $extensionPointName) {
                  return true;
              }
          }
  
          return false;
      }
  
      private function generateExtensionPoints($arrDefines, $arrImplements) {
          $extensionPoints = array();
  
          foreach($arrDefines as $define) {
              $extensionPointName = $define->getExtensionPoint();
              $contextClass = $define->getClassName();
  
              $extensionPoints[$extensionPointName]['context'] = $contextClass;
              $extensionPoints[$extensionPointName]['handlers'] = $this->getHandlersForExtensionPoint($extensionPointName, $arrImplements);
          }
  
          return $extensionPoints;
      }
  
      private function getHandlersForExtensionPoint($extensionPointName, $arrImplements) {
          $handlers = array();
          foreach($arrImplements as $implements) {
              if($implements->getExtensionPoint() != $extensionPointName) {
                  continue;
              }
  
              $temp = array();
              $temp['class'] = $implements->getClassName();
              $temp['method'] = $implements->getMethodName();
              $temp['priority'] = $implements->getPriority();
  
              $handlers[] = $temp;
          }
  
          usort($handlers, array("Gpf_Plugins_EngineSettings", "compareHandlers"));
          
          return $handlers;
      }
  
      static function compareHandlers($a, $b) {
          if ($a['priority'] == $b['priority']) {
              return 0;
          }
          return ($a['priority'] > $b['priority']) ? -1 : 1;
      }
  }
  

} //end Gpf_Plugins_EngineSettings

if (!class_exists('Gpf_Plugins_ExtensionPoint', false)) {
  class Gpf_Plugins_ExtensionPoint extends Gpf_Object {
      /**
       * @var instances of all extension points
       */
      static private $instances = array();
  
      /**
       * extension point name
       */
      private $extensionPointName;
  
      /**
       * name of context class.
       * It is first created on the first use. It must be singleton
       * with getInstance() method
       */
      private $contextClassName = "";
  
      /**
       * class of the context
       * must be singleton with getInstance() method
       */
      private $contextClassObj = null;
  
      /**
       * name of context class.
       * It is first created on the first use. It must be singleton
       * with getInstance() method
       */
      private $handlers = array();
  
      /**
       * array of all process plugins for this extension point
       */
      private $plugins = array();
  
      function __construct($extensionPointName, $definition) {
          $this->extensionPointName = $extensionPointName;
  
          if(!isset($definition['context'])) {
  	        throw Gpf_Plugins_Exception("Extension point '$extensionPointName' does not have context class defined");
          }
          $this->contextClassName = $definition['context'];
  
          if(!isset($definition['handlers']) || !is_array($definition['handlers'])) {
          	throw Gpf_Plugins_Exception("Extension point '$extensionPointName' does not have handlers defined");
          }
          $this->handlers = $definition['handlers'];
      }
  
      /**
       * returns instance of extention point class of given name
       *
       * @return Gpf_Plugins_ExtensionPoint
       */
      public static function getInstance($extensionPointName, $definition) {
      	if(!isset(self::$instances[$extensionPointName])) {
      		self::$instances[$extensionPointName] = new Gpf_Plugins_ExtensionPoint($extensionPointName, $definition);
      	}
          return self::$instances[$extensionPointName];
      }
      
      public static function clear() {
      	self::$instances = array();
      }
  
      /**
       * processes handlers reistered for this extension point
       *
       * @param object $context
       */
      public function processHandlers($context = null) {
      	if(!is_array($this->handlers)) {
      		throw Gpf_Plugins_Exception("Handlers for extension point '".$this->extensionPointName."' are null");
      	}
  
      	//check if definition of extension point contains same context class name as is used in context
      	if (!($context instanceof $this->contextClassName)) {
      	    throw new Gpf_Plugins_Exception("Context class name ($this->contextClassName) is not same as context object (" . get_class($context) . ")");
      	}
  
      	foreach($this->handlers as $handler) {
      	    if(!$this->callHandler($handler, $context)) {
                  break;
              }
      	}
      }
  
      private function callHandler($handler, $context) {
  		$handlerObject = $this->createHandlerObject($handler);
  		$handlerMethod = $this->getHandlerMethod($handler);
  
          try {
              if($context == null) {
                  $returnValue = $handlerObject->$handlerMethod();
              } else {
              	$returnValue = $handlerObject->$handlerMethod($context);
              }
          } catch(Exception $e) {
              throw new Gpf_Plugins_Exception("Unhalted exception: \"".$e->getMessage()."\" in class ".get_class($handlerObject).", STOPPING");
              exit;
          }
  
          if($returnValue === Gpf_Plugins_Engine::PROCESS_STOP_EXIT) {
              exit;
          }
          if($returnValue === Gpf_Plugins_Engine::PROCESS_STOP_EXTENSION_POINT) {
              return false;
          }
          if($returnValue != Gpf_Plugins_Engine::PROCESS_CONTINUE) {
          	// handler function does not need to return value,
          	// it is assumed that it means to continue
           	//   throw new Gpf_Exception("Handler ".get_class($handlerObject).".$handlerMethod() method has to return value PROCESS_CONTINUE / PROCESS_STOP_EXTENSION_POINT / PROCESS_STOP_ALL / PROCESS_STOP_EXIT!");
          }
  
          return true;
      }
  
      private function createHandlerObject($handler) {
      	if(!isset($handler['class'])) {
              throw new Gpf_Plugins_Exception("Handler class is nt defined!");
          }
  
          $className = $handler['class'];
      	// create context object
      	eval("\$obj = $className::getHandlerInstance();");
          return $obj;
      }
  
      private function getHandlerMethod($handler) {
      	if(!isset($handler['method'])) {
              throw new Gpf_Plugins_Exception("Handler method is nt defined!");
          }
  
          return $handler['method'];
      }
  }
  

} //end Gpf_Plugins_ExtensionPoint

if (!class_exists('Gpf_Plugins_Handler', false)) {
  abstract class Gpf_Plugins_Handler extends Gpf_Object {
  
      /**
       * returns instance of handler class.
       * Instance can be either singleton or can create new object for every call
       *
       * @return instance of Gpf_Plugins_Handler child class
       */
      //TODO: This generated warning - not supported in PHP 5.2.x, maybe in next releases of php it will be supported
      //abstract public static function getHandlerInstance();
  }
  

} //end Gpf_Plugins_Handler
