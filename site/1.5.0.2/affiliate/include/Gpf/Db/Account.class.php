<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Account.class.php 27046 2010-02-02 12:30:55Z mkendera $
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
abstract class Gpf_Db_Account extends Gpf_DbEngine_Row {
    const DEFAULT_ACCOUNT_ID = 'default1';
    const APPROVED = 'A';
    const PENDING = 'P';
    const SUSPENDED = 'S';
    const DECLINED = 'D';

    private $password;
    private $firstname;
    private $lastname;

    function __construct(){
        parent::__construct();
        $this->setApplication(Gpf_Application::getInstance()->getCode());
        $date = new Gpf_DateTime();
        $this->setDateinserted($date->toDateTime());
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Accounts::getInstance());
        parent::init();
    }

    function setId($id) {
        $this->set(Gpf_Db_Table_Accounts::ID, $id);
    }

    public function setDefaultId() {
        $this->setId(self::DEFAULT_ACCOUNT_ID);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_Accounts::ID);
    }

    public function setDateinserted($dateInserted) {
        $this->set(Gpf_Db_Table_Accounts::DATEINSERTED, $dateInserted);
    }

    public function getDateinserted() {
        return $this->get(Gpf_Db_Table_Accounts::DATEINSERTED);
    }

    /**
     *
     * @return Gpf_Install_CreateAccountTask
     */
    public function getCreateTask() {
        $task = new Gpf_Install_CreateAccountTask();
        $task->setAccount($this);
        return $task;
    }

    /**
     *
     * @return Gpf_Install_UpdateAccountTask
     */
    public function getUpdateTask() {
        $task = new Gpf_Install_UpdateAccountTask();
        $task->setAccount($this);
        return $task;
    }

    public function createTestAccount($email, $password, $firstName, $lastName) {
        $this->setDefaultId();
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setFirstname($firstName);
        $this->setLastname($lastName);
        $this->getCreateTask()->run(Gpf_Tasks_LongTask::NO_INTERRUPT);
    }

    public function getEmail() {
        return $this->get(Gpf_Db_Table_Accounts::EMAIL);
    }

    public function getPassword() {
        return $this->password;
    }

    public function getStatus() {
        return $this->get(Gpf_Db_Table_Accounts::STATUS);
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setFirstname($name) {
        $this->firstname = $name;
        $this->setName($this->firstname . ' ' . $this->lastname);
    }

    public function setLastname($name) {
        $this->lastname = $name;
        $this->setName($this->firstname . ' ' . $this->lastname);
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function  setName($name) {
        $this->set(Gpf_Db_Table_Accounts::NAME, $name);
    }

    public function  setEmail($email) {
        $this->set(Gpf_Db_Table_Accounts::EMAIL, $email);
    }

    public function setStatus($newStatus) {
        $this->set(Gpf_Db_Table_Accounts::STATUS, $newStatus);
    }

    public function setApplication($application) {
        $this->set(Gpf_Db_Table_Accounts::APPLICATION, $application);
    }

    public function getApplication() {
        return $this->get(Gpf_Db_Table_Accounts::APPLICATION);
    }

    public function getName() {
        return $this->get(Gpf_Db_Table_Accounts::NAME);
    }

    public function setAccountNote($accountNote) {
        $this->set(Gpf_Db_Table_Accounts::ACCOUNT_NOTE, $accountNote);
    }

    public function getAccountNote() {
        return $this->get(Gpf_Db_Table_Accounts::ACCOUNT_NOTE);
    }

    public function setSystemNote($systemNote) {
        $this->set(Gpf_Db_Table_Accounts::SYSTEM_NOTE, $systemNote);
    }

    public function getStystemNote() {
        return $this->get(Gpf_Db_Table_Accounts::SYSTEM_NOTE);
    }
}
?>
