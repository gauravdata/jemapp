<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Record.class.php 20060 2008-08-21 19:02:52Z aharsani $
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
class Gpf_Tasks_Runner extends Gpf_Object {

    const MAX_CRON_PERIOD = 3600; // 60 minutes
    const MAX_TASK_RUN_TIME_WITHOUT_UPDATE = 600;
    const MAX_SERVER_OVERLOADED_INTERRUPTIONS_TIMES = 30;
    const INCLUDE_TASKS = 'I';
    const EXCLUDE_TASKS = 'E';
    

    public function run($timeFrame = 24, $inclusion_type = null, $inclusion_tasks = array()) {
        if($this->isInMaintenanceMode()){
            return;
        }
        $this->updateLastRunTime();
        $this->updateJobsRuns();

        if ($this->isServerOverloaded()) {
            $this->incInterruptionByServerOverloadTimes();
            Gpf_Log::error($this->_('Server overloaded. Task runner interrupted (%s times/runs interrupted).', Gpf_Settings::get(Gpf_Settings_Gpf::SERVER_OVERLOAD_INTERRUPTIONS)));
            if ($this->overloadedInterruptionsTooHigh()) {
                $this->notifyTooMuchServerOverloadsInterruptions();
            } else {
                return;
            }
        }

        $this->resetInterruptionByServerOverloadTimes();
        $this->runExistingTasks($timeFrame, $inclusion_type, $inclusion_tasks);
        $this->scheduleNewTasks($inclusion_type, $inclusion_tasks);
        $this->deleteFinishedTasks($inclusion_type, $inclusion_tasks);
    }

    protected function isInMaintenanceMode() {
        return Gpf_Application::getInstance()->isInMaintenanceMode();
    }

    protected function notifyTooMuchServerOverloadsInterruptions() {
        Gpf_Log::critical('Task runner interrupted due to server overload more than ' . self::MAX_SERVER_OVERLOADED_INTERRUPTIONS_TIMES . ' times!');
    }

    protected function overloadedInterruptionsTooHigh() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::SERVER_OVERLOAD_INTERRUPTIONS) > self::MAX_SERVER_OVERLOADED_INTERRUPTIONS_TIMES) {
            return true;
        }
        return false;
    }

    /**
     * Execute cron manually from UI (request can take max 10 seconds)
     *
     * @service tasks execute
     * @return Gpf_Rpc_Action
     */
    public function manualExecution(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        if(Gpf_Application::getInstance()->isInMaintenanceMode()){
            $action->setInfoMessage($this->_('Upgrade in progress'));
        } else {
            $this->runExistingTasks(10, null, array());
            $this->scheduleNewTasks(null, array());
            $action->setInfoMessage($this->_('Cron request finished.'));
        }
        $action->addOk();
        return $action;
    }

    protected function isServerOverloaded() {
         
    }

    public function isRunningOK() {
        $lastRunTime = $this->getLastRunTime();
        if ($lastRunTime == '' || Gpf_Common_DateUtils::getTimestamp($lastRunTime) < time() - self::MAX_CRON_PERIOD) {
            return false;
        }
        return true;
    }

    public function getLastRunTime() {
        return Gpf_Settings::get(Gpf_Settings_Gpf::LAST_RUN_TIME_SETTING);
    }

    private function updateJobsRuns() {
        $this->deleteFinishedRuns();
         
        $jobsRun = new Gpf_Db_JobsRun();
        $jobsRun->setStartTime(Gpf_Common_DateUtils::now());
        $jobsRun->insert();
    }

    protected function getInvalidRunsMaxTimestamp() {
        return Gpf_Common_DateUtils::getDateTime(time() - (Gpf_Settings::get(Gpf_Settings_Gpf::CRON_RUN_INTERVAL) * 60) - 20);
    }

    private function deleteFinishedRuns() {
        $select = new Gpf_SqlBuilder_DeleteBuilder();
        $select->from->add(Gpf_Db_Table_JobsRuns::getName());
        $select->where->add(Gpf_Db_Table_JobsRuns::STARTTIME, '<', $this->getInvalidRunsMaxTimestamp());
        $select->execute();
    }

    private function deleteFinishedTasks($inclusion_type, $inclusion_tasks) {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_Tasks::getName());
        $delete->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, 'IS NOT', 'NULL', 'AND', false);
        if ($inclusion_type == self::INCLUDE_TASKS) {
            $delete->where->add(Gpf_Db_Table_Tasks::CLASSNAME, 'IN', $inclusion_tasks);
        } else if ($inclusion_type == self::EXCLUDE_TASKS) {
            $delete->where->add(Gpf_Db_Table_Tasks::CLASSNAME, 'NOT IN', $inclusion_tasks);
        }
        $delete->execute();
    }

    private function resetInterruptionByServerOverloadTimes() {
        Gpf_Settings::set(Gpf_Settings_Gpf::SERVER_OVERLOAD_INTERRUPTIONS, 0);
    }

    private function incInterruptionByServerOverloadTimes() {
        Gpf_Settings::set(Gpf_Settings_Gpf::SERVER_OVERLOAD_INTERRUPTIONS, Gpf_Settings::get(Gpf_Settings_Gpf::SERVER_OVERLOAD_INTERRUPTIONS) + 1);
    }

    private function updateLastRunTime() {
        Gpf_Settings::set(Gpf_Settings_Gpf::LAST_RUN_TIME_SETTING, Gpf_Common_DateUtils::now());
    }

    protected function runExistingTasks($timeFrame, $inclusion_type, $inclusion_tasks) {
        $startTime = time();
        $lastRunTaskId = null;
        try {
            while (($timeFrame > (time()- $startTime)) && $task = $this->getPendingTask($lastRunTaskId, $inclusion_type, $inclusion_tasks)) {
                if (($inclusion_type==self::INCLUDE_TASKS && !in_array($task->getClassName(), $inclusion_tasks)) ||
                ($inclusion_type==self::EXCLUDE_TASKS && in_array($task->getClassName(), $inclusion_tasks))) {
                    Gpf_Log::debug('Skipping task ' . $task->getClassName() . ', inclusion settings: ' . $inclusion_type . ', ' . print_r($inclusion_tasks, true));
                    $lastRunTaskId = $task->get(Gpf_Db_Table_Tasks::ID);
                    continue;
                }
                //Init long task
                try {
                    $longTask = $this->createLongTaskObject($task->getClassName());
                } catch (Exception $e) {
                    Gpf_Log::error($this->_("Can not instantiate tasks %s with error %s", $task->getClassName(), $e->getMessage()));
                    $lastRunTaskId = $task->get(Gpf_Db_Table_Tasks::ID);
                    continue;
                }

                //Run long task
                try {
                    Gpf_Log::debug($this->_("Running task %s", $task->getClassName()));
                    $longTask->run($timeFrame - (time()- $startTime), $task);
                } catch (Gpf_Tasks_LongTaskInterrupt $e) {
                } catch (Exception $e) {
                    Gpf_Log::error($this->_("Error while running task %s. Message: %s. Trace: %s.", $task->getClassName(), $e->getMessage(), $e->getTraceAsString()));
                }
                $lastRunTaskId = $task->get(Gpf_Db_Table_Tasks::ID);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::debug('No task pending');
        }
    }

    protected function createLongTaskObject($className) {
        return Gpf::newObj($className);
    }

    /**
     * @param Gpf_Data_Record $record
     * @return Gpf_Db_Task
     */
    private function getTask(Gpf_Data_Record $record) {
        $task = new Gpf_Db_Task();
        $task->fillFromRecord($record);
        $task->setPersistent(true);
        return $task;
    }

    private function findLastRunTaskRecordsetPosition(Gpf_Data_RecordSet $recordset, $lastRunTaskId) {
        $recordId = null;
        foreach ($recordset as $key => $record) {
            if ($lastRunTaskId == $record->get(Gpf_Db_Table_Tasks::ID)) {
                $recordId = $key;
                break;
            }
        }
        return $recordId;
    }

    /**
     *
     * @return Gpf_Db_Task
     */
    protected function getPendingTask($lastRunTaskId, $inclusion_type, $inclusion_tasks) {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->addAll(Gpf_Db_Table_Tasks::getInstance());
        $sql->from->add(Gpf_Db_Table_Tasks::getName());
        $sql->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
        $sql->where->add(Gpf_Db_Table_Tasks::TYPE, '=', Gpf_Db_Task::TYPE_CRON);

        if ($inclusion_type == self::INCLUDE_TASKS) {
            $sql->where->add(Gpf_Db_Table_Tasks::CLASSNAME, 'IN', $inclusion_tasks);
        } else if ($inclusion_type == self::EXCLUDE_TASKS) {
            $sql->where->add(Gpf_Db_Table_Tasks::CLASSNAME, 'NOT IN', $inclusion_tasks);
        }

        $andCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $andCondition->add(Gpf_Db_Table_Tasks::IS_EXECUTING, '<>', Gpf::YES, 'OR');
        $orCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $orCondition->add(Gpf_Db_Table_Tasks::IS_EXECUTING, '=', Gpf::YES);
        $orCondition->add(Gpf_Db_Table_Tasks::DATECHANGED, '<', Gpf_Common_DateUtils::getDateTime(time() - self::MAX_TASK_RUN_TIME_WITHOUT_UPDATE));
        $andCondition->addCondition($orCondition, 'OR');
        $sql->where->addCondition($andCondition);

        $sleepCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $sleepCondition->add(Gpf_Db_Table_Tasks::SLEEP_UNTIL, '=', null, 'OR');
        $sleepCondition->add(Gpf_Db_Table_Tasks::SLEEP_UNTIL, '<=', Gpf_Common_DateUtils::now(), 'OR');

        $sql->where->addCondition($sleepCondition);

        $sql->orderBy->add(Gpf_Db_Table_Tasks::WORKING_AREA_TO.'-'.Gpf_Db_Table_Tasks::WORKING_AREA_FROM, false);
        $sql->orderBy->add(Gpf_Db_Table_Tasks::DATECHANGED);

        $recordset = $sql->getAllRows();
        if ($recordset->getSize() == 0) {
            return false;
        }

        if ($lastRunTaskId == null) {
            return $this->getTask($recordset->get(0));
        }

        $recordId = $this->findLastRunTaskRecordsetPosition($recordset, $lastRunTaskId);

        if ($recordId===null) {
            return $this->getTask($recordset->get(0));
        }
        if ($recordId==$recordset->getSize()-1) {
            return false;
        }
        if ($recordset->get($recordId+1) == null) {
            return false;
        }
        return $this->getTask($recordset->get($recordId+1));
    }

    protected function scheduleNewTasks($inclusion_type, $inclusion_tasks) {
        $taskScheduler = new Gpf_Tasks_Scheduler();
        $taskScheduler->scheduleTasks($inclusion_type, $inclusion_tasks);
    }
}

?>
