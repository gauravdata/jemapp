<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Paths.class.php 34226 2011-08-16 09:38:01Z mkendera $
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
class Gpf_Paths {
    const INSTALLER = 'installer';
    const ACCOUNTS_DIR = 'accounts/';
    const TEMPLATES_DIR = 'themes/';
    const INSTALL_DIR = 'install/';
    const CACHE_DIRECTORY = 'cache/';
    const FILES_DIRECTORY = 'files/';
    const DEFAULT_THEME = '_common_templates/';
    const SCRIPTS_DIR = 'scripts/';
    const CONFIG_DIR = 'config/';
    const IMAGE_DIR = 'img/';
    const WALLPAPER_DIR = 'wallpapers/';
    const PLUGINS_DIR = 'plugins/';

    private static $instance;

    private $cachedSearchPaths = array();
    private $cacheTopPath = null;
    private $cacheAccountDirectoryPath = null;
    private $cacheTemplateRelativePath = null;
    private $cacheIncludePaths = null;

    private $projectIncludePath = array();
    private $smartyPluginsPaths = array();
    private $serverPaths = array();
    private $frameworkPath;
    private $clientPaths = array();
    private $baseurl;
    private $theme = '';
    private $installMode;
    private $scriptRelativePath = '';
    /**
     * @var string script path relative to the application root. '' if script is not run from application root
     */
    private $scriptPath = '';

    protected function __construct($frameworkPath, array $serverPaths, array $clientPaths, $scriptDir, $pathsDir) {
        $this->computeScriptPaths($scriptDir, $pathsDir);
        $this->frameworkPath = self::normalizePath($this->scriptRelativePath . $this->addTrailingSlash($frameworkPath));
        foreach ($serverPaths as $path) {
            $this->serverPaths[] = self::normalizePath($this->scriptRelativePath . $this->addTrailingSlash($path));
        }
        $this->serverPaths[] = $this->frameworkPath;
        foreach ($clientPaths as $clientPath) {
            $this->clientPaths[] = self::normalizePath($this->scriptRelativePath . $this->addTrailingSlash($clientPath));
        }
        try {
            $this->setIncludePath();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    private function computeScriptPaths($scriptDir, $pathsDir) {
        $pos = strrpos($pathsDir, 'scripts');
        if (substr(strtolower($pathsDir), 0, $pos) == substr(strtolower($scriptDir), 0, $pos)) {
            $this->scriptPath = $this->addTrailingSlash(substr($scriptDir, $pos));
        } else {
            $this->scriptPath = '';
        }
        $this->scriptRelativePath = self::computePathsDifference($scriptDir, rtrim($pathsDir, 'scripts'));
    }

    private function getSmartyIncludePath() {
        return $this->frameworkPath . 'include/Smarty/';
    }

    public function getSmartyPluginsPaths() {
        return $this->smartyPluginsPaths;
    }

    public function addSmartyPluginPath($path) {
        $this->smartyPluginsPaths[] = $path;
    }

    public function getIncludePath() {
        $path = $this->getSmartyIncludePath() . PATH_SEPARATOR .
        $this->frameworkPath . 'include/tcpdf/' . PATH_SEPARATOR .
        $this->frameworkPath . 'include/pclzip/' . PATH_SEPARATOR .
        $this->frameworkPath . 'include/Pear/' . PATH_SEPARATOR .
                    './' . PATH_SEPARATOR;
        foreach ($this->projectIncludePath as $projectIncludePath) {
            foreach ($this->serverPaths as $serverPath) {
                $path .= $serverPath . $projectIncludePath . PATH_SEPARATOR;
            }
        }
        return $path . get_include_path();
    }

    public function addProjectIncludePath($path) {
        $this->projectIncludePath[] = $path;
    }

    public function setIncludePath() {
        $smartyPath = rtrim($this->getSmartyIncludePath(), '/\\');

        $includePath = $this->getIncludePath();

        if(false === set_include_path($includePath) && false === ini_set('include_path', $includePath)) {
            if(false === strpos(get_include_path(), $smartyPath)) {
                throw new Exception('Could not set include path. Please set it in your php.ini: include_path=' . $includePath);
            }
        }
    }

    /**
     * @return Gpf_Paths
     */
    public static function getInstance() {
        if (self::$instance == null) {
            throw new Gpf_Exception("Gpf_Paths not initialized. You must call Gpf_Paths::create() first.");
        }
        return self::$instance;
    }

    public static function create($frameworkPath, array $serverPaths = array(), $clientPaths = array('../client/'), $scriptDir = null, $pathsDir = null) {
        $stack = debug_backtrace();
        $file1 = $stack[count($stack) - 1]['file'];
        $file2 = $stack[count($stack) - 2]['file'];
        if(defined('CONVERT_TO_REAL_PATHS')) {
            $file1 = realpath($file1);
            $file2 = realpath($file2);
        }
        if ($scriptDir===null) {
            $scriptDir = str_replace('\\', '/', dirname($file1));
        }
        if ($pathsDir===null) {
            $pathsDir = str_replace('\\', '/', dirname($file2));
        }
        self::$instance = new Gpf_Paths($frameworkPath, $serverPaths, $clientPaths, $scriptDir, $pathsDir);
    }

    private static function computePathsDifference($pathFrom, $pathTo) {
        $pathFromLowerCase = strtolower($pathFrom);
        $pathToLowerCase = strtolower($pathTo);
        $maxCommonPartLength = min(array(strlen($pathFromLowerCase), strlen($pathToLowerCase)));
        for ($i=0; $i<$maxCommonPartLength && $pathFromLowerCase[$i] == $pathToLowerCase[$i]; $i++) {
        }
        $pathFrom = substr($pathFrom, $i);
        $pathTo = rtrim(substr($pathTo, $i), 'scripts');
        $arrDirs = explode('/', $pathFrom);
        $count = count($arrDirs);
        if (!strlen($arrDirs[0])) {
            $count = 0;
        }
        return str_repeat('../', $count).$pathTo;
    }

    /**
     * @return Gpf_Paths
     */
    public function clonePaths($theme) {
        $clone = clone $this;
        $clone->theme = $theme;
        $clone->cachedSearchPaths = array();
        return $clone;
    }

    /**
     * @return boolean
     */
    public function isDevelopementVersion() {
        return ($this->frameworkPath != $this->serverPaths[0]);
    }

    /**
     * @return array
     */
    public function getIncludePaths() {
        if($this->cacheIncludePaths === null) {
            $standardIncludePaths = $this->getDirectoryPaths($this->serverPaths, 'include/');
            $pluginsIncludePaths = $this->getPluginsPaths();
            $encodedIncludePaths = $this->getEncodedPaths();

            $this->cacheIncludePaths = array_merge($standardIncludePaths, $pluginsIncludePaths, $encodedIncludePaths);
        }

        return $this->cacheIncludePaths;
    }

    private function getEncodedPaths() {
        // We dont support zend encoded versions yet.
        /*
         if(function_exists('zend_optimizer_version')) {
         return $this->getAdditionalPaths('include/zend/');
         }
         */
        if(extension_loaded('ionCube Loader')) {
            return $this->getAdditionalPaths('include/ion/');
        }
        return array();
    }

    /**
     * returns array of possible plugin directories
     *
     * @return array
     */
    private function getAdditionalPaths($postfixFolder) {
        $clearPaths = array();
        foreach($this->serverPaths as $path) {
            if($path == '/') {
                continue;
            }
            $clearPaths[$path . $postfixFolder] = $path . $postfixFolder;
        }
        return $clearPaths;
    }

    /**
     * returns array of possible plugin directories
     *
     * @return array
     */
    public function getPluginsPaths() {
        return $this->getAdditionalPaths(self::PLUGINS_DIR);
    }

    /**
     * @return string path to top template directory including trailing slash
     */
    public function getTopTemplatePath() {
        return $this->getTopPath() . $this->getTemplateRelativePath();
    }

    public function getThemesTemplatePaths() {
        $dirs = array();
        foreach ($this->serverPaths as $dir) {
            if ($dir == $this->frameworkPath) {
                continue;
            }
            $dirs[] = $dir;
        }
        return $this->getDirectoryPaths($dirs, $this->getTemplateRelativePath());
    }

    /**
     * Get list of available wallpaper directories in selected
     *
     * @return array
     */
    public function getThemeWallPaperDirPaths() {
        $paths = array();
        foreach ($this->getTemplateSearchPaths() as $path) {
            $wallpaperDir = $path . self::IMAGE_DIR . self::WALLPAPER_DIR;
            if (file_exists($wallpaperDir)) {
                $paths[] = $wallpaperDir;
            }
        }
        return $paths;
    }


    public function getFrameworkTemplatesPath() {
        return $this->getFrameworkPath() . $this->getTemplateRelativePath();
    }

    public function getTopTemplateUrl() {
        return $this->getBaseServerUrl() . $this->getTemplateRelativePath();
    }

    private function getTemplateRelativePath() {
        if($this->cacheTemplateRelativePath === null) {
            $this->cacheTemplateRelativePath = $this->computeTemplateRelativePath();
        }
        return $this->cacheTemplateRelativePath;
    }

    private function computeTemplateRelativePath() {
        if($this->isInstallModeActive() || $this->isDevelopementVersion()) {
            return self::INSTALL_DIR . self::TEMPLATES_DIR;
        }
        return self::ACCOUNTS_DIR . $this->getAccountId() . '/' . self::TEMPLATES_DIR;
    }

    /**
     * @return array
     */
    public function getClientPaths() {
        return $this->getDirectoryPaths($this->clientPaths, 'src/');
    }

    /**
     * @param string $moduleName
     * @return string path to to compiled javascript code of module
     */
    public function getGwtModuleUrl($moduleName, $gzipped = false) {
        $suffix = '.nocache.js';
        if($gzipped) {
            $suffix = '.g.nocache.js';
        }
        foreach ($this->clientPaths as $clientPath) {
            if ($this->isDevelopementVersion()) {
                $fileName = $clientPath . 'www/' . $moduleName . '/' . $moduleName . $suffix;
            } else {
                $fileName =  $this->scriptRelativePath . $this->scriptPath . 'js/' . $moduleName . $suffix;
            }
            $file = new Gpf_Io_File($fileName);
            if ($file->isExists()) {
                return $this->pathToUrl($fileName);
            }
        }
        throw new Gpf_Exception($fileName . ' file does not exist.');
    }

    public function getStyleSheetUrl($styleSheetName, $onlyDefault = false) {
        return $this->getResourceUrl($styleSheetName, '', '', $onlyDefault);
    }

    public function getTemplatePath($templateName, $panelName = '') {
        return $this->getResourcePath($templateName, '', $panelName);
    }

    /**
     * Get Image Url for input imageFileName.
     * Function will search resources on disc and return first match.
     *
     * @param string $imageFileName
     * @param string $panelName
     * @return string
     */
    public function getImageUrl($imageFileName, $panelName = '') {
        return $this->getResourceUrl($imageFileName, self::IMAGE_DIR, $panelName, false);
    }

    public function getTheme() {
        if ($this->theme == "") {
            $this->theme = Gpf_Session::getAuthUser()->getTheme();
        }
        return $this->theme;
    }

    public function buildTemplatePath($path, $panelName, $theme, $postDirectory = '') {
        $panelPath = $this->addTrailingSlash($panelName);
        if($panelName == '') {
            $panelPath = '';
        }
        return $this->addTrailingSlash($path .
        $this->getTemplateRelativePath() .
        $panelPath .
        $this->addTrailingSlash($theme) .
        $postDirectory);

    }

    public function getTemplateSearchPaths($panelName = '', $postDirectory = '', $onlyDefault = false) {
        $key = $panelName . '|' . $postDirectory . '|' . $onlyDefault;
        if(isset($this->cachedSearchPaths[$key])) {
            return $this->cachedSearchPaths[$key];
        }

        $paths = array();

        if ($panelName == '') {
            $panelName = $this->addTrailingSlash(Gpf_Session::getModule()->getPanelName());
        } else {
            $panelName = $this->addTrailingSlash($panelName);
        }

        foreach ($this->serverPaths as $path) {
            if($path == $this->frameworkPath && $this->isDevelopementVersion()) {
                $panel = '';
            } else {
                $panel = $panelName;
            }
            if (!$onlyDefault && $panel != '') {
                $paths[] = $this->buildTemplatePath($path, $panel, $this->getTheme(), $postDirectory);
            }
            $paths[] = $this->buildTemplatePath($path, $panel, self::DEFAULT_THEME, $postDirectory);
        }
        $this->cachedSearchPaths[$key] = $paths;
        return $paths;
    }


    public function getResourceUrl($resouceName, $postDirectory = '', $panelName = '', $onlyDefault = false) {
        return $this->pathToUrl($this->getResourcePath($resouceName, $postDirectory, $panelName, $onlyDefault));
    }

    private static function normalizePath($path) {
        $normalizedPath = str_replace('/./', '/', $path);
        if ($normalizedPath == '/') {
            return './';
        }
        return $normalizedPath;
    }

    private function pathToUrl($path) {
        $url = Gpf_Paths::getInstance()->getBaseServerUrl() . substr($path, strlen($this->scriptRelativePath));
        return self::normalizePath($url);
    }

    public function getResourcePath($resouceName, $postDirectory = '', $panelName = '', $onlyDefault = false) {
        if($postDirectory != '') {
            $postDirectory = $this->addTrailingSlash($postDirectory);
        }
        $paths = $this->getTemplateSearchPaths($panelName, $postDirectory, $onlyDefault);
        foreach ($paths as $path) {
            $fileName = $path . $resouceName;
            if (Gpf_Io_File::isFileExists($fileName)) {
                return $fileName;
            }
        }
        throw new Gpf_ResourceNotFoundException($resouceName, $panelName);
    }

    public function getFrameworkPath() {
        return $this->frameworkPath;
    }

    public function getScriptPath() {
        return $this->scriptPath;
    }

    /**
     * @return string relative url to base application directory directory with trailing slash
     */
    public function getFrameworkUrl() {
        return $this->frameworkPath;
    }

    /**
     * @param string $directory directory name with trailing slash
     * @return array
     */
    private function getDirectoryPaths(array $paths, $directory) {
        $directoryPaths = array();
        foreach ($paths as $path) {
            $directoryPaths[] = $this->addTrailingSlash($path . $directory);
        }
        return $directoryPaths;
    }

    public function getTopPath() {
        if($this->cacheTopPath === null) {
            $this->cacheTopPath = str_replace('\\', '/', $this->addTrailingSlash($this->serverPaths[0]));
        }
        return $this->cacheTopPath;
    }

    public function getAccountDirectoryPath() {
        if($this->cacheAccountDirectoryPath === null) {
            $this->cacheAccountDirectoryPath = $this->computeAccountDirectoryPath();
        }
        return $this->cacheAccountDirectoryPath;
    }

    private function computeAccountDirectoryPath() {
        if($this->isInstallModeActive() || $this->isMissingAccountDirectory()) {
            return $this->getInstallDirectoryPath();
        }
        return $this->getAccountDirectoryPathExact();
    }

    private function getAccountDirectoryPathExact() {
        return $this->addTrailingSlash($this->getAccountsPath() . $this->getAccountId());
    }

    public function getAccountConfigDirectoryPath() {
        return $this->getAccountDirectoryPath() . self::CONFIG_DIR;
    }

    public function getRealAccountDirectoryPath() {
        if($this->isMissingAccountDirectory()) {
            throw new Gpf_Exception("Real account directory doesn't exist");
        }
        return $this->addTrailingSlash($this->getAccountsPath() . $this->getAccountId());
    }

    public function getRealAccountConfigDirectoryPath() {
        return $this->getRealAccountDirectoryPath() . self::CONFIG_DIR;
    }

    public function getAccountDirectoryRelativePath() {
        return $this->addTrailingSlash(self::ACCOUNTS_DIR . $this->getAccountId());
    }

    public function getAccountsPath() {
        return $this->addTrailingSlash($this->getTopPath() . self::ACCOUNTS_DIR);
    }

    public static function getAffiliateSignupUrl() {
        return Gpf_Paths::getInstance()->getFullBaseServerUrl() .  'affiliates/signup.php';
    }

    public function getAccountPath() {
        return $this->addTrailingSlash($this->getAccountsPath() . $this->getAccountId());
    }

    public function getFullBaseServerPath() {
        return $this->addTrailingSlash(realpath($this->getTopPath()));
    }

    public function getFullAccountPath() {
        return Gpf_Paths::getInstance()->getFullBaseServerPath() . Gpf_Paths::getInstance()->getAccountDirectoryRelativePath();
    }

    public function getFullScriptsPath() {
        return $this->getFullBaseServerPath() . self::SCRIPTS_DIR;
    }

    public function getFullScriptsUrl() {
        return $this->getFullBaseServerUrl() . self::SCRIPTS_DIR;
    }

    /*
     * usable only at linux machines (we using it on our hosted accounts)
     */
    public static function getServerHostName() {
        return exec('hostname');
    }

    /*
     * usable only at on our hosted accounts
     */
    public function getServerHostedAccountId() {
        preg_match('/^\/home\/([^\/]*)\/.*/i', $this->getFullBaseServerPath(), $matches);
        if (!is_array($matches) || count($matches)==0 || count($matches)==1) {
            throw new Gpf_Exception('Account id can not be found');
        }
        return $matches[1];
    }

    private function getServerName() {
        try {
            return Gpf_Settings::get(Gpf_Settings_Gpf::SERVER_NAME);
        } catch (Exception $e) {
            return $this->resolveServerName();
        }
    }

    private function resolveServerName() {
        $serverNameSetting = Gpf_Settings::get(Gpf_Settings_Gpf::SERVER_NAME_RESOLVE_FROM);
        if (isset($_SERVER[$serverNameSetting])) {
            return $_SERVER[$serverNameSetting];
        }
        return '';
    }

    public function getFullDomainUrl() {
        $portString = '';
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80
        && $_SERVER['SERVER_PORT'] != 443) {
            $portString = ':' . $_SERVER["SERVER_PORT"];
        }
        $protocol = 'http';
        if(isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0 && strtolower($_SERVER['HTTPS']) != 'off') {
            $protocol = 'https';
        }
        return $protocol . '://' . $this->getServerName() . $portString;
    }

    public function getFullBaseServerUrl() {
        return $this->getFullDomainUrl() . $this->getBaseServerUrl();
    }

    public function getFullAccountServerUrl() {
        return $this->getFullBaseServerUrl() . self::ACCOUNTS_DIR . $this->getAccountId() . "/";
    }

    public function getBaseServerUrl() {
        if($this->isDevelopementVersion()) {
            return $this->getBaseUrl() . 'server/';
        }
        return $this->getBaseUrl();
    }

    public function setInstallMode($active) {
        $this->installMode = $active;
    }

    public function isInstallModeActive() {
        return $this->installMode || Gpf_Session::getInstance()->getVar(self::INSTALLER);
    }

    public function isMissingAccountDirectory() {
        return !file_exists($this->getAccountsPath() . $this->getAccountId() . '/');
    }

    public function getInstallDirectoryPath() {
        return $this->getTopPath() . Gpf_Paths::INSTALL_DIR;
    }

    private function getBaseUrl() {
        if ($this->baseurl === null) {
            try {
                $this->baseurl = Gpf_Settings::get(Gpf_Settings_Gpf::BASE_SERVER_URL);
            } catch (Exception $e) {
                $this->baseurl = $this->resolveBaseUrl();
            }
        }
        return $this->baseurl;
    }

    private function resolveBaseUrl() {
        if (array_key_exists('PATH_INFO', $_SERVER) && @$_SERVER['PATH_INFO'] != '') {
            $scriptName = str_replace('\\', '/', @$_SERVER['PATH_INFO']);
        } else {
            if (array_key_exists('SCRIPT_NAME', $_SERVER)) {
                $scriptName = str_replace('\\', '/', @$_SERVER['SCRIPT_NAME']);
            } else {
                $scriptName = '';
            }
        }
        if (strlen($scriptName) > 0 && $scriptName[strlen($scriptName)-1] == '/') {
            $url = rtrim($scriptName, '/');
        } else {
            $url = dirname($scriptName);
        }
        if (array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
            $dir = str_replace('\\', '/', dirname(@$_SERVER['SCRIPT_FILENAME']));
        } else {
            $dir = '';
        }

        while ($dir != '.' && $dir != '/' && $dir != '' && !$this->isBaseServerDir($dir)) {
            $url = str_replace('\\', '/', dirname($url));
            $dir = str_replace('\\', '/', dirname($dir));
        }
        $this->baseurl = rtrim(str_replace('\\', '/', $url), '/') . '/';
        return $this->baseurl;
    }

    protected function isBaseServerDir($dir) {
        $required_path = '/include';
        if ($this->isDevelopementVersion()) {
            $required_path = '/client';
        }
        return file_exists($dir . $required_path);
    }

    private function addTrailingSlash($path) {
        if (strlen($path) == 0) {
            return $path;
        }
        return rtrim($path, '/\\') . '/';
    }

    public function getLanguageDirectory() {
        if ($this->isInstallModeActive()) {
            return $this->getLanguageInstallDirectory();
        } else {
            return $this->getLanguageAccountDirectory();
        }
    }

    public function getLanguageAccountDirectory() {
        return $this->getAccountDirectoryPathExact() . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
    }

    public function getLanguageInstallDirectory() {
        return $this->getInstallDirectoryPath() . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
    }


    public function getLanguageCacheDirectory() {
        if ($this->isInstallModeActive()) {
            return $this->getLanguageCacheInstallDirectory();
        } else {
            return $this->getLanguageCacheAccountDirectory();
        }
    }

    public function getCacheAccountDirectory() {
        return $this->getAccountDirectoryPath() . self::CACHE_DIRECTORY;
    }

    public function getLanguageCacheAccountDirectory() {
        return $this->getCacheAccountDirectory() . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
    }

    public function getLanguageCacheInstallDirectory() {
        return $this->getInstallDirectoryPath() . self::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
    }

    public function getPluginDirectoryUrl() {
        return $this->getBaseServerUrl() . self::PLUGINS_DIR;
    }

    public function getFrameworkPluginsDirectoryUrl() {
        if ($this->isDevelopementVersion()) {
            return $this->getFrameworkUrl() . self::PLUGINS_DIR;
        }
        return $this->getBaseServerUrl() . self::PLUGINS_DIR;
    }

    public function saveServerUrlSettings() {
        if (($serverName = $this->resolveServerName()) != '') {
            Gpf_Settings::set(Gpf_Settings_Gpf::SERVER_NAME, $serverName);
        }
        Gpf_Settings::set(Gpf_Settings_Gpf::BASE_SERVER_URL, $this->resolveBaseUrl());
    }

    /**
     * @return String
     */
    private function getAccountId() {
        return Gpf_Application::getInstance()->getAccountId();
    }
}
?>
