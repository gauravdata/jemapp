<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_Table_QuickTasks extends Gpf_DbEngine_Table {
    const ID = 'quicktaskid';
    const ACCOUNTID = 'accountid';
    const GROUPID = 'groupid';
    const REQUEST = 'request';
    const VALIDTO = 'validto';

    /**
     *
     * @var Gpf_Db_Table_QuickTasks
     */
    private static $instance;

    /**
     *
     * @return Gpf_Db_Table_QuickTasks
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_quicktasks');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 16, true);
        $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
        $this->createColumn(self::GROUPID, self::CHAR, 16);
        $this->createColumn(self::REQUEST, self::CHAR);
        $this->createColumn(self::VALIDTO, self::DATETIME);
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::ID)));
    }

    public function removeTasksAfterExecute(Gpf_Db_QuickTask $quickTask) {
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(self::getName());
        if ($quickTask->getGroupId() == '') {
            $deleteBuilder->where->add(self::ID, "=", $quickTask->getId());
        } else {
            $deleteBuilder->where->add(self::GROUPID, "=", $quickTask->getGroupId());
        }
        $deleteBuilder->delete();
    }
}

?>
