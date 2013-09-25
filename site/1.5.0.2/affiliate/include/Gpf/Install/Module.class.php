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
abstract class Gpf_Install_Module extends Gpf_ModuleBase {
    const SCENARIO_STEPS = 'scenarioSteps';
    const SCENARIO = 'scenario';
    
    protected function getTitle() {
        return Gpf_Application::getInstance()->getName() . ' - ' . $this->_('Installer');
    }
    
    protected function checkApplication() {
    }
        
    protected function initSession() {
        Gpf_Paths::getInstance()->setInstallMode(true);
        parent::initSession();
        Gpf_Install_Manager::create($this->createInstallManager());
    }
    
    abstract protected function createInstallManager();
        
    protected function initCachedData() {
        parent::initCachedData();
        Gpf_Rpc_CachedResponse::addById(Gpf_Install_Manager::getInstance()->getScenarioDescription(), self::SCENARIO);
        Gpf_Rpc_CachedResponse::addById(Gpf_Install_Manager::getInstance()->getScenarioSteps(), self::SCENARIO_STEPS);
    }
}
?>
