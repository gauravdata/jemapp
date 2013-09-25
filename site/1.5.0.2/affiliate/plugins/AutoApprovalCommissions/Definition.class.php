<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class AutoApprovalCommissions_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'AutoApprovalCommissions';
        $this->name = $this->_('Automatic approval of commissions');
        $this->description = $this->_('This plugin will automatically approve commissions after selected amount of days. Do not forget to configure plugin after activation.');
        $this->version = '1.0.0';
        $this->addRequirement('PapCore', '4.2.3.2');

        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.initFields',
            'AutoApprovalCommissions_Main', 'initFields');

        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.save',
            'AutoApprovalCommissions_Main', 'save');

        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.load',
            'AutoApprovalCommissions_Main', 'load');

    }

    public function onActivate() {
        $taskRunner = new Gpf_Tasks_Runner();
        if (!$taskRunner->isRunningOK()) {
            throw new Gpf_Exception($this->_('Auto approval of commissions plugin require cron job which is not running now. Please set it up in Tools -> Integration -> Cron Job Integration'));
        }
        $this->addAutoApprovalCommissionsTask();
    }

    public function onDeactivate() {
        $this->deleteAutoApprovalCommissionsTask();
    }


	private function addAutoApprovalCommissionsTask() {
		$plannedTask = $this->createPlannedTask();
		try {
            $plannedTask->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $plannedTask->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_DAILY);
            $plannedTask->insert();
        }
	}

	private function deleteAutoApprovalCommissionsTask() {
		try {
            $plannedTask = $this->createPlannedTask();
            $plannedTask->loadFromData();
            $plannedTask->delete();
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
	}

	private function createPlannedTask() {
		$plannedTask = new Gpf_Db_PlannedTask();
        $plannedTask->setClassName('AutoApprovalCommissions_Runner');
        return $plannedTask;
	}
}
?>
