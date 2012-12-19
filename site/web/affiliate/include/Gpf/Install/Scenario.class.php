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
abstract class Gpf_Install_Scenario extends Gpf_Object {
    const STEP_CODE = 'code';
    const STEP_NAME = 'name';
    
    const VERSION = 'version';
    const SCENARION_NAME = 'name';
    
    
    private $steps = array();
    private $currentStep;
    protected $name;
    
    public function __construct() {
        $this->initSteps();
        $this->currentStep = 0;
    }
    
    protected abstract function initSteps();
    
    protected function addStep(Gpf_Install_Step $step) {
        $this->steps[] = $step;
    }

    public function getCurrentStepCode() {
        return $this->steps[$this->currentStep]->getCode();
    }
    
    public function getNextStep() {
        $this->setNextStep();
        return $this->getCurrentStepCode();
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    public function getSteps() {
        $steps = new Gpf_Data_RecordSet();
        $steps->setHeader(array(self::STEP_CODE, self::STEP_NAME, 'selected'));
        foreach ($this->steps as $index => $step) {
            $record = $steps->createRecord();
            $record->set(self::STEP_CODE, $step->getCode());
            $record->set(self::STEP_NAME, $step->getName());
            if($this->currentStep == $index) {
                $record->set('selected', Gpf::YES);
            } else {
                $record->set('selected', Gpf::NO);
            }
            $steps->addRecord($record);
        }
        return $steps;
    }

    /**
     *
     * @return Gpf_Rpc_Form
     */
    public function getDescription() {
        $data = new Gpf_Rpc_Form();
        $data->setField(self::VERSION, Gpf_Application::getInstance()->getVersion());
        $data->setField(self::SCENARION_NAME, $this->name);
        return $data;
    }

    private function setNextStep() {
        if($this->currentStep +1 >= count($this->steps)) {
            throw new Gpf_Exception($this->_('Internal error'));
        }
        $this->currentStep++;
    }
    
    public function getName() {
        return $this->name;
    }
    
    abstract public function silentDevelopmentInstall();
}
?>
