<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: LoggingForm.class.php 24764 2009-07-03 13:43:36Z mjancovic $
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
abstract class Pap_Merchants_Config_TaskSettingsFormBase extends Gpf_Object {

	protected function insertTask($className) {    
		$task = $this->createTask($className);
		try {
			$task->loadFromData($this->getTaskLoadColumns());
		} catch (Gpf_DbEngine_NoRowException $e) {
			$task->insert();
		} catch (Gpf_DbEngine_TooManyRowsException $e) {
		}
	}

	protected function removeTask($className) {
		$task = $this->createTask($className);
		try {
			$task->loadFromData($this->getTaskLoadColumns());
			$task->delete();
		} catch (Gpf_DbEngine_NoRowException $e) {
		}
	}
	
    protected function getFieldValue(Gpf_Rpc_Form $form, $fieldName) {
    	if($form->existsField($fieldName)) {
    		return $form->getFieldValue($fieldName);
    	}
    	return '';
    }
    
    /**
     * @return array
     */
    protected function getTaskLoadColumns() {
    	return array(
				Gpf_Db_Table_PlannedTasks::CLASSNAME, 
				Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID
				);
    }
    
    protected function initAccountId(Gpf_Db_PlannedTask $task) {   	
    }

	/**
	 * @param String $className
	 * @return Gpf_Db_PlannedTask
	 */
	private function createTask($className) {
		$task = new Gpf_Db_PlannedTask();
		$task->setClassName($className);
		$task->setRecurrencePresetId('A');
		$task->setParams($this->getLastDateParams());
		$this->initAccountId($task);
		return $task;
	}
	
	private function getLastDateParams() {
	    $params = array('lastdate' => Gpf_Common_DateUtils::now());
	    return serialize($params);
	}
}

?>
