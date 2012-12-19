<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Engine.class.php 34226 2011-08-16 09:38:01Z mkendera $
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

?>
