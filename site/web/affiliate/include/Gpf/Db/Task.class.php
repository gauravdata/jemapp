<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Db_Task extends Gpf_DbEngine_Row implements Gpf_Tasks_Task {

    const TYPE_USER = 'U';
    const TYPE_CRON = 'C';
    
    public function init() {
        $this->setTable(Gpf_Db_Table_Tasks::getInstance());
        parent::init();
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_Tasks::ID, $id);
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_Tasks::ID);
    }

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Tasks::ACCOUNTID, $accountId);
    }
    
    public function setWorkingAreaFrom($from) {
        return $this->set(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, $from);
    }
    
    public function setWorkingAreaTo($to) {
        return $this->set(Gpf_Db_Table_Tasks::WORKING_AREA_TO, $to);
    }
    
    public function getWorkingAreaFrom() {
    	return $this->get(Gpf_Db_Table_Tasks::WORKING_AREA_FROM);
    }
    
    public function getWorkingAreaTo() {
        return $this->get(Gpf_Db_Table_Tasks::WORKING_AREA_TO);
    }

    public function setProgress($progress) {
        $this->set(Gpf_Db_Table_Tasks::PROGRESS, $progress);
    }

    public function getProgress() {
        return $this->get(Gpf_Db_Table_Tasks::PROGRESS);
    }

    public function setClassName($className) {
        $this->set(Gpf_Db_Table_Tasks::CLASSNAME, $className);
    }

    public function getClassName() {
        return $this->get(Gpf_Db_Table_Tasks::CLASSNAME);
    }

    public function setParams($params) {
        $this->set(Gpf_Db_Table_Tasks::PARAMS, $params);
    }

    public function getParams() {
        return $this->get(Gpf_Db_Table_Tasks::PARAMS);
    }

    public function getDateFinished() {
        return $this->get(Gpf_Db_Table_Tasks::DATEFINISHED);
    }

    public function isFinished() {
        return $this->getDateFinished() != null;
    }

    public function getSleepUntil() {
        return $this->get(Gpf_Db_Table_Tasks::SLEEP_UNTIL);
    }
    
    public function getAccountId() {
    	return $this->get(Gpf_Db_Table_Tasks::ACCOUNTID);
    }
    
    public function setType($type) {
        $this->set(Gpf_Db_Table_Tasks::TYPE, $type);
    }

    /**
     *
     * @param $sleepUntil Gpf_DateTime
     */
    public function setSleepUntil(Gpf_DateTime $sleepUntil) {
        $this->set(Gpf_Db_Table_Tasks::SLEEP_UNTIL, $sleepUntil->toDateTime());
    }

    public function setSleepTime($sleepSeconds) {
        $time = new Gpf_DateTime();
        $time->addSecond($sleepSeconds);
        $this->setSleepUntil($time);
    }

    public function insert() {
        $this->set(Gpf_Db_Table_Tasks::DATECREATED, Gpf_Common_DateUtils::now());
        $this->set(Gpf_Db_Table_Tasks::DATECHANGED, Gpf_Common_DateUtils::now());
        if (is_null($this->getAccountId()) || $this->getAccountId() === '') {
        	$this->set(Gpf_Db_Table_Tasks::ACCOUNTID, Gpf_Session::getInstance()->getAuthUser()->getAccountId());
        }
        $this->setProgressMessage($this->_('Waiting'));
        parent::insert();
    }

    public function setPid($pid) {
        $this->set(Gpf_Db_Table_Tasks::PID, $pid);
    }

    public function update($updateColumns = array()) {
        if (count($updateColumns) > 0 && !array_key_exists(Gpf_Db_Table_Tasks::DATECHANGED, $updateColumns)) {
            $updateColumns[] = Gpf_Db_Table_Tasks::DATECHANGED;
        }
        $this->set(Gpf_Db_Table_Tasks::DATECHANGED, Gpf_Common_DateUtils::now());
        parent::update($updateColumns);
    }

    public function finish() {
        $this->setIsExecuting(false);
        $this->setProgressMessage('');
        $this->set(Gpf_Db_Table_Tasks::DATEFINISHED, Gpf_Common_DateUtils::now());
        $this->save();
    }

    public function finishTask() {
        $this->finish();
    }

    public function updateTask() {
        $this->update((array(Gpf_Db_Table_Tasks::PARAMS, Gpf_Db_Table_Tasks::PROGRESS, Gpf_Db_Table_Tasks::PROGRESS_MESSAGE, Gpf_Db_Table_Tasks::SLEEP_UNTIL, Gpf_Db_Table_Tasks::WORKING_AREA_FROM, Gpf_Db_Table_Tasks::WORKING_AREA_TO)));
    }

    public function insertTask() {
        $this->insert();
    }

    public function loadTask($className, $params) {
        $this->setClassName($className);
        $this->setParams($params);
        $this->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
        $this->loadFromData();
    }


    public function setName($name) {
        $this->set(Gpf_Db_Table_Tasks::NAME, $name);
    }

    public function getName() {
        return $this->get(Gpf_Db_Table_Tasks::NAME);
    }

    public function setProgressMessage($message) {
        $this->set(Gpf_Db_Table_Tasks::PROGRESS_MESSAGE, $message);
    }

    public function getProgressMessage() {
        return $this->get(Gpf_Db_Table_Tasks::PROGRESS_MESSAGE);
    }

    /**
     * Set is_executing parameter
     *
     * @param $isExecuting boolean (true/false) or string value (Y/N)
     */
    public function setIsExecuting($isExecuting) {
        if ($isExecuting == true || $isExecuting == Gpf::YES) {
            $this->set(Gpf_Db_Table_Tasks::IS_EXECUTING, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_Tasks::IS_EXECUTING, Gpf::NO);
        }
    }

    /**
     * Get parameter is_executing
     *
     * @return string
     */
    public function getIsExecuting() {
        return $this->get(Gpf_Db_Table_Tasks::IS_EXECUTING);
    }

    /**
     * Return boolean information if current task is under execution
     * @return boolean
     */
    public function isExecuting() {
        return $this->getIsExecuting() == Gpf::YES;
    }

    public function lockTask($isExecuting) {
        $this->setIsExecuting($isExecuting);
        $this->update(array(Gpf_Db_Table_Tasks::IS_EXECUTING));
    }
}

?>
