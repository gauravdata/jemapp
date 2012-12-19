<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Features_RecurringCommissions_Config extends Gpf_Plugins_Config {
    const API_TRIGGERRED_COMMISSIONS = 'ApiTriggerredCommissions';
    
    protected function initFields() {
        $this->addCheckBox($this->_('Use API to trigger commissions'), self::API_TRIGGERRED_COMMISSIONS, 
            $this->_('When you check this option, recurring commissions won\'t be given automatically, but you wil have to trigger them using API. More details at: %s',
                     '<a href="'.Gpf_Application::getKnowledgeHelpUrl('programmers-documentation/api/high-level-api/papapirecurringcommission.html').' target="_blank">'.Gpf_Application::getKnowledgeHelpUrl('programmers-documentation/api/high-level-api/papapirecurringcommission.html').'</a>'));        
    }
    
    /**
     * @anonym
     * @service recurring_commissions_config write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        if ($form->getFieldValue(self::API_TRIGGERRED_COMMISSIONS) == Gpf::YES) {
            $this->deleteRecurringCommissionsTask();
        } else {
            $this->addRecurringCommissionsTask();
        }
        
        $form->setInfoMessage($this->_('Recurring commission settings saved'));
        return $form;
    }

    /**
     * @anonym
     * @service recurring_commissions_config read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $apiTriggerred = Gpf::NO;
        try {
            $this->createPlannedTask()->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $apiTriggerred = Gpf::YES;
        }
        $form->addField(self::API_TRIGGERRED_COMMISSIONS, $apiTriggerred);
        return $form;
    }
    
    public function deleteRecurringCommissionsTask() {
        try {
            $plannedTask = $this->createPlannedTask();
            $plannedTask->loadFromData();
            $plannedTask->delete();
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
    }
    
    public function addRecurringCommissionsTask() {
        $plannedTask = $this->createPlannedTask();
        try {
            $plannedTask->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $plannedTask->setRecurrencePresetId('each15m');
            $plannedTask->insert();
        }
    }
    
    /**
     * @return Gpf_Db_PlannedTask
     */
    private function createPlannedTask() {
        $plannedTask = new Gpf_Db_PlannedTask();
        $plannedTask->setClassName('Pap_Features_RecurringCommissions_Runner');
        return $plannedTask;
    }
}

?>
