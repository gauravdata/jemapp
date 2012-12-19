<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Log.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
?>
