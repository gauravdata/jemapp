<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
abstract class Gpf_Tasks_LongTask extends Gpf_Object {
    const DEFAULT_MAX_WORKERS_COUNT = 1;

    const NO_INTERRUPT = -1;
    const MEMORY_SAFE_OFFSET = 2097152;

    /**
     * @var Gpf_Tasks_Task
     */
    protected $task;

    private $resumed = false;
    private $params;
    private $workingAreaFrom = 0;
    private $workingAreaTo = 0;
    private $startTime;
    private $skipping = true;
    private $progress;
    protected $doneProgress;
    private $progressMessage;
    private $doneMessage;
    protected $maxRunTime;
    protected $memoryLimit = null;

    protected function createWorker($workingRangeFrom, $workingRangeTo) {
    }

    protected function getMaxWorkersCount() {
        return self::DEFAULT_MAX_WORKERS_COUNT;
    }

    /**
     * @var Gpf_Settings_AccountSettings
     */
    protected $accountSettings;

    protected function getMemoryLimit() {
        if ($this->memoryLimit === null) {
            $this->memoryLimit  = Gpf_Install_Requirements::getMemoryLimit();
        }
        return $this->memoryLimit;
    }

    protected function checkIfMemoryIsFull ($memory) {
        if (!defined('TASK_MEMORY_SAFE_OFFSET')) {
            $offset = self::MEMORY_SAFE_OFFSET;
        } else {
            $offset = TASK_MEMORY_SAFE_OFFSET;
        }
        if ($memory + $offset < $this->getMemoryLimit()) {
            return false;
        }
        return true;
    }

    protected function setWorkingArea($from, $to) {
        $this->workingAreaFrom = $from;
        $this->workingAreaTo = $to;
    }

    protected function setParams($params) {
        $this->params = $params;
    }

    protected function getParams() {
        return $this->params;
    }

    protected function getProgress() {
        return $this->doneProgress;
    }

    protected function setProgress($progress) {
        $this->doneProgress = $progress;
    }

    protected function getStartTime() {
        return $this->startTime;
    }

    public function resume() {
        if ($this->task != null && !$this->task->isFinished()) {
            $this->loadFromTask();
            return true;
        }
        $this->task = $this->createTask();
        try {
            $this->loadTask();
            $this->loadFromTask();
            return true;
        } catch (Gpf_Exception $e) {
            return false;
        }
    }

    protected function loadTask() {
        $this->task->loadTask(get_class($this), $this->params);
    }

    protected function loadFromTask() {
        $this->params = $this->task->getParams();
        $this->doneProgress = $this->task->getProgress();
    }

    protected function isDone($code, $message = '') {
        return !$this->isPending($code, $message);
    }

    protected function isPending($code, $message = '') {
        if($this->maxRunTime < 0) {
            return true;
        }

        if($this->isProcessed($code)) {
            return false;
        }
        $this->changeProgress($code, $message);
        $this->checkInterruption();
        return true;
    }

    protected function changeProgress($code, $message) {
        $this->progressMessage = $message;
        $this->progress = $code;
    }

    private function isProcessed($code) {
        if(!$this->resumed) {
            return false;
        }
        if(!$this->skipping) {
            return false;
        }
        if($code == $this->task->getProgress()) {
            $this->skipping = false;
        }
        return true;

    }

    protected function checkInterruption() {
        if($this->isTimeToInterrupt()) {
            $this->interrupt(0);
        }
    }

    protected function isTimeToInterrupt() {
        return (time() - $this->startTime > $this->maxRunTime);
    }

    protected function setDone() {
        $this->doneProgress = $this->progress;
        $this->doneMessage = $this->progressMessage;
    }

    protected function setDoneAndInterrupt() {
        $this->doneProgress = $this->progress;
        $this->doneMessage = $this->progressMessage;
        $this->interrupt(0);
    }

    /**
     * Interupt task
     * @param $sleepSeconds Define minimum how many seconds should task wait until can be again executed
     */
    protected function interrupt($sleepSeconds = 0) {
        if($this->maxRunTime < 0) {
            return;
        }
        $this->updateTask($sleepSeconds);
        throw new Gpf_Tasks_LongTaskInterrupt($this->getProgressMessage());
    }

    protected function getProgressMessage() {
        if ($this->progress == $this->doneProgress) {
            return $this->doneMessage . '...' . $this->_('DONE');
        }
        return $this->progressMessage . '...' . $this->_('IN PROGRESS');
    }

    protected function init() {
    }

    abstract protected function execute();

    abstract public function getName();

    private function runWithoutInterrupt() {
        $this->init();
        $this->lock();
        $this->execute();
        $this->unlock();
    }

    public function setTask($task) {
        $this->task = $task;
        if ($this->task != null) {
            $this->loadFromTask();
        }
    }

    public function forceFinishTask() {
        $this->task->finishTask();
    }

    protected function imMasterWorker() {
        if ($this->task->getWorkingAreaFrom() == 0) {
            return true;
        }
        return false;
    }

    protected function canBeSplit() {
        if ($this->getActualWorkersCount() < $this->getAvaliableWorkersCount()) {
            return true;
        }
        return false;
    }

    protected function getClassName() {
        return '';
    }

    /**
     * @return Gpf_Db_Task
     */
    private function getMaxFreeWorker() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Gpf_Db_Table_Tasks::getInstance());
        $select->from->add(Gpf_Db_Table_Tasks::getName());
        $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
        $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
        $select->orderBy->add(Gpf_Db_Table_Tasks::WORKING_AREA_TO.'-'.Gpf_Db_Table_Tasks::WORKING_AREA_FROM, false);
        $select->limit->set(0, 1);

        $workerId = $select->getOneRow()->get(Gpf_Db_Table_Tasks::ID);
        $task = new Gpf_Db_Task();
        $task->setId($workerId);
        $task->load();
        return $task;
    }
    
    protected function slaveExist($workingAreaFrom, $workingAreaTo) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
        $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
        $select->where->add(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, '=', $workingAreaFrom);
        $select->where->add(Gpf_Db_Table_Tasks::WORKING_AREA_TO, '=', $workingAreaTo);
        $count = $this->getTableRowsCount($select, Gpf_Db_Table_Tasks::getName());
        $this->debug('There are ' . $count . ' num of slaves for ' . $workingAreaFrom . ' index');
        return $count > 0;
    }

    protected function getTableRowsCount(Gpf_SqlBuilder_SelectBuilder $select, $tableName) {
        $select->from->add($tableName);
        $select->select->add('count(*)', 'cnt');
        $record = $select->getOneRow();
        return $record->get('cnt');
    }

    protected function getActualWorkersCount() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
        $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
        return $this->getTableRowsCount($select, Gpf_Db_Table_Tasks::getName());
    }

    protected function getAvaliableWorkersCount() { 
        $select = new Gpf_SqlBuilder_SelectBuilder();
        return $this->getTableRowsCount($select, Gpf_Db_Table_JobsRuns::getName());
    }

    private function imBiggestWorker() {
        $biggestTask = $this->getMaxFreeWorker();

        if (($this->task->getWorkingAreaTo() - $this->task->getWorkingAreaFrom()) >= ($biggestTask->getWorkingAreaTo() - $biggestTask->getWorkingAreaFrom())) {
            return true;
        }
        return false;
    }

    protected function splitMe() {
        $this->debug("May I split?");
        if (($this->imBiggestWorker() || ($this->imMasterWorker()) ) && ($this->task->getWorkingAreaTo() - $this->task->getWorkingAreaFrom()) >= 1) {
            $this->splitTask($this->task);
        }
    }

    protected function resetMyWorkingArea() {
        $this->debug("Im resetting my working area");
        $this->task->setWorkingAreaFrom(0);
        $this->task->setWorkingAreaTo($this->getMaxWorkersCount()-1);
        $this->task->updateTask();
    }

    protected function imAlone() {
        if ($this->getActualWorkersCount()<=1) {
            return true;
        }
        return false;
    }

    private function doMasterWorkBeforeExecute() {
        $this->debug('Master work before execute...');
        if($this->syncPointReached() && $this->imAlone()) {
            $this->resetMyWorkingArea();
            $this->debug('Master work at syncpoint...');
            $this->doMasterWorkWhenSyncPointReached();
        }
    }

    private function doSlaveWorkBeforeExecute() {
        $this->debug('Slave work before execute...');
        if($this->syncPointReached()) {
            $this->debug('Slave work at syncpoint...');
            $this->doSlaveWorkWhenSyncPointReached();
        }
    }

    protected function doMasterWorkAfterExecute() {
    }

    protected function doSlaveWorkAfterExecute() {
    }

    protected function doMasterWorkWhenSyncPointReached() {
    }

    protected function doSlaveWorkWhenSyncPointReached() {
    }

    protected function syncPointReached() {
        return false;
    }

    private function doBeforeExecute() {
        $this->debug('Before execute');
        if ($this->imMasterWorker()) {
            $this->doMasterWorkBeforeExecute();
        } else {
            $this->doSlaveWorkBeforeExecute();
        }
        if ($this->getMaxWorkersCount() > 1 && $this->canBeSplit()) {
            if ($this->imAlone()) {
                $this->debug('Im alone, planning work for slaves...');
                $this->createSlaves();
            } else {
                $this->debug('Im not alone, just can split my self...');
                $this->splitMe();
            }
        }
    }

    protected function createSlaves() {
        $avaliableWorkersCount = $this->getAvaliableWorkersCount();
        if ($this->getActualWorkersCount() < $avaliableWorkersCount) {
            try {
                while ($avaliableWorkersCount > $this->getActualWorkersCount()) {
                    $worker = $this->getMaxFreeWorker();
                    if (($worker->getWorkingAreaTo() - $worker->getWorkingAreaFrom()) <= 0) {
                        $this->debug('Splitting is not possible anymore... scheduling is over');
                        break;
                    }
                    $this->splitTask($worker);
                }
            } catch (Gpf_DbEngine_NoRowException $e) {
                $this->debug('Error during creating new slave: ' . $e->getMessage());
                return;
            }
        }
    }

    protected function splitTask(Gpf_Db_Task $task) {
        $workingAreaTo = $task->getWorkingAreaTo();
        $splitNumber = intval(($task->getWorkingAreaTo() - $task->getWorkingAreaFrom())/2);
        $task->setWorkingAreaTo($task->getWorkingAreaFrom() + $splitNumber);
        $task->update();
        if ($task->get(Gpf_Db_Table_Tasks::ID) == $this->task->get(Gpf_Db_Table_Tasks::ID)) {
            $this->task = $task;
        } 
         
        $this->createWorker($task->getWorkingAreaFrom() + $splitNumber + 1, $workingAreaTo);
    }

    private function doAfterExecute() {
        if ($this->imMasterWorker() || $this->getActualWorkersCount() == 1) {
            if ($this->imAlone() && !$this->syncPointReached()) {
                $this->debug('Master finished his work, but sync point was not reached. Rescheduling...');
                $this->resetMyWorkingArea();
            }
            $this->doMasterWorkAfterExecute();
        } else if (!$this->imMasterWorker()) {
            $this->doSlaveWorkAfterExecute();
        }
        $this->setDone();
    }

    final public function run($maxRunTime = 24, Gpf_Tasks_Task $task = null) {
        $this->maxRunTime = $maxRunTime;
        $this->setTask($task);
        $this->initSettings();
        if($this->maxRunTime < 0) {
            $this->runWithoutInterrupt();
            return;
        }
        $this->startTime = time();

        $this->resumed = true;
        if(!$this->resume()) {
            $this->resumed = false;
            $this->init();
            $this->insertTask();
        }

        if(strlen($this->task->getProgress()) == 0) {
            $this->resumed = false;
        }

        try {
            $this->lock();
            $this->doBeforeExecute();
            $this->execute();
            $this->doAfterExecute();
            $this->unlock();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $this->doAfterLongTaskInterrupt();
            $this->unlock();
            throw $e;
        } catch (Exception $e) {
            //in case of error, don't execute task next 30 seconds again
            Gpf_Log::error(sprintf('Task %s threw exception %s', get_class($this), $e));
            $this->updateTask(30);
            $this->unlock();
            throw $e;
        }

        $this->task->finishTask();
    }
    
    protected function doAfterLongTaskInterrupt() {
        $this->updateTask(15);
    }

    protected function initSettings() {
        if (!is_null($this->task) && !is_null($this->task->getAccountId()) && $this->task->getAccountId() !== '') {
            $this->accountSettings = Gpf_Settings::getAccountSettings($this->task->getAccountId());
            return;
        }
        $this->accountSettings = Gpf_Settings::getAccountSettings(Gpf_Application::getInstance()->getAccountId());
    }

    private function lock() {
        if ($this->task != null) {
            $this->task->lockTask(true);
        }
    }

    private function unlock() {
        if ($this->task != null) {
            $this->task->lockTask(false);
        }
    }

    public function insertTask() {
        $this->task = $this->createTask();
        $this->task->setClassName(get_class($this));
        $this->task->setParams($this->params);
        $this->task->setWorkingAreaFrom($this->workingAreaFrom);
        $this->task->setWorkingAreaTo($this->workingAreaTo);
        $this->task->setName($this->getName());
        $this->task->setProgressMessage($this->getProgressMessage());
        if(false !== ($pid = @getmypid())) {
            $this->task->setPid($pid);
        }
        $this->task->insertTask();
    }

    /**
     * @return Gpf_Tasks_Task
     */
    protected function createTask() {
        $task = new Gpf_Db_Task();
        $task->setType($this->getTaskType());
        return $task;
    }

    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_CRON;
    }

    protected function updateTask($sleepSeconds = 0) {
        if($this->doneProgress !== null) {
            $this->task->setParams($this->params);
            $this->task->setSleepTime($sleepSeconds);
            $this->task->setProgress($this->doneProgress);
            $this->task->setProgressMessage($this->getProgressMessage());
            $this->updateTaskObject();
        }
    }

    protected function updateTaskObject() {
        $this->task->updateTask();
    }

    /**
     * Returns true if user can delete from user interface task.
     * System tasks, which should not be deleted should return always false
     *
     * @return boolean
     */
    public function canUserDeleteTask() {
        return false;
    }

    protected function debug($message) {
        Gpf_Log::debug($message);
    }
}

?>
