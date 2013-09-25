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
abstract class Gpf_Install_Manager extends Gpf_Object {
    
    protected static $instance;
    /**
     * @var Gpf_Install_Scenario
     */
    protected $scenario;
    
    /**
     *
     * @param Gpf_Install_Manager $instance
     * @return Gpf_Install_Manager
     */
    public static function create(Gpf_Install_Manager $instance) {
        Gpf_Session::getInstance()->setVar(Gpf_Paths::INSTALLER, $instance);
        return self::$instance = $instance;
    }
    
    /**
     * @return Gpf_Install_Manager
     */
    public static function getInstance() {
        if(self::$instance === null) {
            throw new Gpf_Exception("Instance not initialized");
        }
        return self::$instance;
    }
    
    protected function __construct() {
        Gpf_Paths::getInstance()->setInstallMode(true);
        $this->initScenario();
    }

    abstract protected function initScenario();
    
    public function __wakeup() {
        self::$instance = $this;
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getScenarioSteps() {
        return $this->scenario->getSteps();
    }
    
    /**
     *
     * @return Gpf_Install_Scenario
     */
    public function getScenario() {
        return $this->scenario;
    }
    
    /**
     *
     * @return Gpf_Rpc_Form
     */
    public function getScenarioDescription() {
        return $this->scenario->getDescription();
    }
    
    public function getCurrentStep() {
        return $this->scenario->getCurrentStepCode();
    }
    
    public function getNextStep() {
        return $this->scenario->getNextStep();
    }
}
?>
