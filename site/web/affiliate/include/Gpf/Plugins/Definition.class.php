<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
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
class Gpf_Plugins_Definition extends Gpf_Object {
    const CODE = 'id';
    const NAME = 'name';
    const URL = 'url';
    const DESCRIPTION = 'description';
    const VERSION = 'version';
    const AUTHOR = 'author';
    const AUTHOR_URL = 'author_url';
    const ACTIVE = 'active';
    const HELP = 'help';
    const CONFIG_CLASS_NAME = 'conf_service';

    const PLUGIN_TYPE_SYSTEM = "S";
    const PLUGIN_TYPE_NORMAL = "N";
    const PLUGIN_TYPE_FEATURE = "F";

    protected $codeName;
    protected $name;
    protected $url;
    protected $description;
    protected $version;
    protected $author = 'Quality Unit, s.r.o.';
    protected $authorUrl;

    /**
     * Text of help
     *
     * @var unknown_type
     */
    protected $help;

    protected $configurationClassName;

    /**
     * System plugin will be not displayed in list of plugins and is always activated
     *
     * @var boolean
     */
    protected $pluginType = self::PLUGIN_TYPE_NORMAL;

    private $arrDefines = array();
    private $arrImplements = array();
    private $arrRequirements = array();
    private $arrRejected = array();

    /**
     * @param Gpf_Data_RecordSet $recordset
     * @return Gpf_Data_Record
     */
    public function toRecord(Gpf_Data_RecordSet $recordset) {
        $record = $recordset->createRecord();

        $record->set(self::CODE, $this->getCodeName());
        $record->set(self::NAME, $this->getName());
        $record->set(self::URL, $this->getUrl());
        $record->set(self::DESCRIPTION, $this->getDescription());
        $record->set(self::VERSION, $this->getVersion());
        $record->set(self::AUTHOR, $this->getAuthor());
        $record->set(self::AUTHOR_URL, $this->getAuthorUrl());
        $record->set(self::HELP, $this->getHelp());
        $record->set(self::ACTIVE, 'N');
        $record->set(self::CONFIG_CLASS_NAME, $this->getConfigurationClassName());

        return $record;
    }

    public function getCodeName() {
        return $this->codeName;
    }

    public function getName() {
        return $this->name;
    }

    public function getUrl() {
        return Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK);
    }

    public function getDescription() {
        return $this->description;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getAuthorUrl() {
        return Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK);
    }

    public function getHelp() {
        return $this->help;
    }

    /**
     * Add extension point definition
     *
     * @param string $extensionPoint Name of extension point
     * @param string $className Class name of input context
     */
    protected function addDefine($extensionPoint, $className) {
         $this->arrDefines[] = new Gpf_Plugins_Definition_ExtensionPoint($extensionPoint, $className);
    }

    /**
     * Extension point with lower priority is executed later
     */
    protected function addImplementation($extensionPoint, $className, $methodName, $priority = 10) {
         $this->arrImplements[] = new Gpf_Plugins_Definition_ExtensionPoint($extensionPoint, $className, $methodName, $priority);;
    }

    protected function addRequirement($pluginCode, $minVersion) {
        $this->arrRequirements[] = new Gpf_Plugins_Definition_VersionRequirement($pluginCode, $minVersion);
    }
    
    protected function addRefuse($pluginCode) {    
    	$this->arrRejected[] = $pluginCode;	
    }

    /**
     * Return array of extension point definitions (Gpf_Plugins_Definition_ExtensionPoint)
     */
    public function getDefines() {
        return $this->arrDefines;
    }

    /**
     * Return array of extension point implementations (Gpf_Plugins_Definition_ExtensionPoint)
     */
    public function getImplements(){
        return $this->arrImplements;
    }

    /**
     * Get default priority value.
     * Overwrite this method if is required different priority level.
     *
     * @return int
     */
    public function getPriority() {
        return 10;
    }

    public function check() {
    	$this->checkRequirements();
        $this->checkRejectedPlugins();
    }
    
    public function checkRequirements() {
        foreach ($this->arrRequirements as $requirement) {
            $requirement->check();
        }
    }
    
    public function checkRejectedPlugins() {
        foreach ($this->arrRejected as $pluginCode) {
			if (Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($pluginCode)) {
            	throw new Gpf_Exception($this->_("%s refused active %s plugin", $this->codeName, $pluginCode));
			}
        }
    }

    /**
     * Method will be called, when plugin is activated. e.g. create some tables required by plugin.
     *
     * @throws Gpf_Exception when plugin can not be activated
     */
    public function onActivate() {
    }

    /**
     * Method will be called, when plugin is deactivated. e.g. drop some tables needed by plugin.
     *
     */
    public function onDeactivate() {
    }

    /**
     * Is plugin normal plugin ? If yes, it willnot  be displayed in list of plugins and will be always activated.
     *
     * @return boolean
     */

    public function isSystemPlugin() {
        return $this->pluginType == self::PLUGIN_TYPE_SYSTEM;
    }

    public function getPluginType() {
        return $this->pluginType;
    }

    public function getConfigurationClassName() {
        return $this->configurationClassName;
    }
}

class Gpf_Plugins_Definition_VersionRequirement extends Gpf_Object {
    private $pluginCode;
    private $minVersion;

    public function __construct($pluginCode, $minVersion) {
        $this->pluginCode = $pluginCode;
        $this->minVersion = $minVersion;
    }

    public function check() {
        if (!Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($this->pluginCode)) {
            throw new Gpf_Exception($this->_("Required plugin %s is not active", $this->pluginCode));
        }
        $plugin = Gpf_Plugins_Engine::getInstance()->findPlugin($this->pluginCode);
        if (version_compare($plugin->getVersion(), $this->minVersion) < 0) {
            throw new Gpf_Exception($this->_("Required plugin %s has to be in version %s or higher", $this->pluginCode, $this->minVersion));
        }
    }
}
?>
