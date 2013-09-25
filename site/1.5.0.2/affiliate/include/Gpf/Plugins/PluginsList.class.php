<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
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
class Gpf_Plugins_PluginsList extends Gpf_Object {

    /**
     * activate this plugin
     *
     * @service plugin write
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function activate(Gpf_Rpc_Params $params) {
        return $this->pluginChangeState($params, true);
    }

    /**
     *
     * @param $params
     * @param $activate
     * @return Gpf_Rpc_Action
     */
    private function pluginChangeState(Gpf_Rpc_Params $params, $activate) {
        $action = new Gpf_Rpc_Action($params);
        $code = $action->getParam("code");

        if (defined('ENABLE_ENGINECONFIG_LOG')) {
            Gpf_Log::info('pluginChangeState: ' . $code . ', activate:' . $activate);
        }
        
        $engineConfig = Gpf_Plugins_Engine::getInstance();

        try {
            $engineConfig->activate($code, $activate);
            $engineConfig->saveConfiguration();
            if($activate) {
                $action->setInfoMessage($this->_('Plugin was successfuly activated'));
            } else {
                $action->setInfoMessage($this->_('Plugin was successfuly deactivated'));
            }
            $action->addOk();
        } catch (Exception $e) {
        	 if (defined('ENABLE_ENGINECONFIG_LOG')) {
        	   Gpf_Log::error('pluginChangeState-error: ' . $e->getMessage());
        	 }
            $action->setErrorMessage($e->getMessage());
            $action->addError();
        }
        return $action;
    }

    /**
     * activate this plugin
     *
     * @service plugin write
     * @param $fields
     */
    public function deactivate(Gpf_Rpc_Params $params) {
        return $this->pluginChangeState($params, false);
    }

    /**
     * returns available plugins
     *
     * @service plugin read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $availablePlugins = Gpf_Plugins_Engine::getInstance()->getAvailablePlugins();
        if ($params->exists("type")) {
            $type = $params->get("type");
        } else {
            $type = Gpf_Plugins_Definition::PLUGIN_TYPE_NORMAL;
        }

        $response = new Gpf_Data_RecordSet();
        $response->addColumn(Gpf_Plugins_Definition::CODE);
        $response->addColumn(Gpf_Plugins_Definition::NAME);
        $response->addColumn(Gpf_Plugins_Definition::URL);
        $response->addColumn(Gpf_Plugins_Definition::DESCRIPTION);
        $response->addColumn(Gpf_Plugins_Definition::VERSION);
        $response->addColumn(Gpf_Plugins_Definition::AUTHOR);
        $response->addColumn(Gpf_Plugins_Definition::AUTHOR_URL);
        $response->addColumn(Gpf_Plugins_Definition::ACTIVE);
        $response->addColumn(Gpf_Plugins_Definition::HELP);
        $response->addColumn(Gpf_Plugins_Definition::CONFIG_CLASS_NAME);

        foreach($availablePlugins as $plugin) {
            if ($plugin->getPluginType() == $type) {
                $response->addRecord($plugin->toRecord($response));
            }
        }

        $response = $this->setActivePlugins($response);

        return $response;
    }

    private function setActivePlugins(Gpf_Data_RecordSet $inputResult) {
        $actualConfiguration = Gpf_Plugins_Engine::getInstance()->getConfiguration();

        if($actualConfiguration === null) {
            if (defined('ENABLE_ENGINECONFIG_LOG')) {
                Gpf_Log::error('Plugin engine configuration is incorrect');
            }
            throw new Gpf_Exception("Plugin engine configuration is incorrect");
        }

        foreach($inputResult as $record) {
            $codename = $record->get(Gpf_Plugins_Definition::CODE);

            if($actualConfiguration->isPluginActive($codename)) {
                if (defined('ENABLE_ENGINECONFIG_LOG')) {
                    Gpf_Log::info('setActivePlugins with plugin: ' . $codename . ' - activating');
                }
                $record->set(Gpf_Plugins_Definition::ACTIVE, 'Y');
            } else {
                if (defined('ENABLE_ENGINECONFIG_LOG')) {
                    Gpf_Log::info('setActivePlugins with plugin: ' . $codename . ' - deactivating');
                }
                $record->set(Gpf_Plugins_Definition::ACTIVE, 'N');
            }
        }

        return $inputResult;
    }
}
?>
