<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Tasks_SessionTask implements Gpf_Tasks_Task  {

    const TASKS = 'Tasks';

    private $pid;
    private $accountId;
    private $className;
    private $params;
    private $progress;
    private $progressMessage;
    private $name;
    private $isExecuting;
    private $workingAreaFrom;
    private $workingAreaTo;

    public function __construct() {
        $this->accountId = Gpf_Session::getInstance()->getAuthUser()->getAccountId();
    }

    public function setProgressMessage($message) {
        $this->progressMessage = $message;
    }

    public function getProgressMessage() {
        return $this->progressMessage;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getWorkingAreaTo() {
        return $this->workingAreaTo;
    }
    
    public function getWorkingAreaFrom() {
        return $this->workingAreaFrom;
    }
    
    public function setWorkingAreaTo($to) {
        $this->workingAreaTo = $to;
    }
    
    public function setWorkingAreaFrom($from) {
        $this->workingAreaFrom = $from;
    }

    public function getName() {
        return $this->name;
    }

    public function setProgress($progress) {
        $this->progress = $progress;
    }

    public function getProgress() {
        return $this->progress;
    }

    public function setClassName($className) {
        $this->className = $className;
    }

    public function getClassName() {
        return $this->className;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }

    public function isFinished() {
        try {
            $this->getTaskFromSession($this->className, $this->params);
        } catch (Gpf_Exception $e) {
            return true;
        }
        return false;
    }

    public function setPid($pid) {
        $this->pid = $pid;
    }

    public function insertTask() {
        $this->save();
    }

    public function updateTask() {
        $this->save();
    }

    public function loadTask($className, $params) {
        $this->initTask(unserialize($this->getTaskFromSession($className, $params)));
    }

    public function finishTask() {
        if (($tasks = Gpf_Session::getInstance()->getVar($this->getAccountTasksName())) == false ||
        !array_key_exists($this->getTaskName($this->className, $this->params), $tasks)) {
            return;
        }
        unset($tasks[$this->getTaskName($this->className, $this->params)]);
        Gpf_Session::getInstance()->setVar($this->getAccountTasksName(), $tasks);
    }

    private function save() {
        if (($tasks = Gpf_Session::getInstance()->getVar($this->getAccountTasksName())) == false) {
            $tasks = array();
        }
        $tasks[$this->getTaskName($this->className, $this->params)] = serialize($this);
        Gpf_Session::getInstance()->setVar($this->getAccountTasksName(), $tasks);
    }

    private function getAccountTasksName() {
        return self::TASKS . '_' . $this->accountId;
    }

    private function getTaskName($className, $params) {
        return sha1($className . $params);
    }

    private function getTaskFromSession($className, $params) {
        if (($tasks = Gpf_Session::getInstance()->getVar($this->getAccountTasksName())) == false) {
            throw new Gpf_Exception($this->getAccountTasksName() . ' not exist');
        }
        if (!array_key_exists($this->getTaskName($className, $params), $tasks)) {
            throw new Gpf_Exception('Task not exist');
        }
        return $tasks[$this->getTaskName($className, $params)];
    }

    private function initTask(Gpf_Tasks_SessionTask $task) {
        $this->pid = $task->pid;
        $this->accountId = $task->accountId;
        $this->className = $task->className;
        $this->params = $task->params;
        $this->progress = $task->progress;
        $this->progressMessage = $task->progressMessage;
        $this->name = $task->name;
        $this->isExecuting = $task->isExecuting;
    }

    public function lockTask($isExecuting) {
        $this->isExecuting = $isExecuting;
    }

    public function setSleepTime($sleepSeconds) {

    }
}

?>
