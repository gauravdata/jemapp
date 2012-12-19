<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Tasks extends Gpf_DbEngine_Table {
    const ID = 'taskid';
    const ACCOUNTID = 'accountid';
    const CLASSNAME = 'classname';
    const PARAMS = 'params';
    const PROGRESS = 'progress';
    const DATECREATED = 'datecreated';
    const DATECHANGED = 'datechanged';
    const DATEFINISHED = 'datefinished';
    const PID = 'pid';
    const NAME = 'name';
    const PROGRESS_MESSAGE = 'progress_message';
    const IS_EXECUTING = 'is_executing';
    const SLEEP_UNTIL = 'sleepuntil';
    const TYPE = 'rtype';
    const WORKING_AREA_FROM = 'workingareafrom';
    const WORKING_AREA_TO = 'workingareato';

    /**
     *
     * @var Gpf_Db_Table_Tasks
     */
    private static $instance;

    /**
     *
     * @return Gpf_Db_Table_Tasks
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_tasks');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
        $this->createColumn(self::CLASSNAME, self::CHAR);
        $this->createColumn(self::PARAMS, self::CHAR);
        $this->createColumn(self::PROGRESS, self::CHAR);
        $this->createColumn(self::DATECREATED, self::DATETIME);
        $this->createColumn(self::DATECHANGED, self::DATETIME);
        $this->createColumn(self::DATEFINISHED, self::DATETIME);
        $this->createColumn(self::SLEEP_UNTIL, self::DATETIME);
        $this->createColumn(self::PID, self::CHAR, 40);
        $this->createColumn(self::NAME, self::CHAR, 255);
        $this->createColumn(self::PROGRESS_MESSAGE, 'text');
        $this->createColumn(self::IS_EXECUTING, self::CHAR, 1, true);
        $this->createColumn(self::TYPE, self::CHAR, 1);
        $this->createColumn(self::WORKING_AREA_FROM, self::INT);
        $this->createColumn(self::WORKING_AREA_TO, self::INT);
    }
}

?>
