<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailOutboxGrid.class.php 24330 2009-05-06 08:05:53Z jsimon $
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
class Gpf_Tasks_PendingTasksGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Tasks::DATECREATED, $this->_("Created"), true);
        $this->addViewColumn(Gpf_Db_Table_Tasks::DATECHANGED, $this->_("Updated"), true);
        $this->addViewColumn(Gpf_Db_Table_Tasks::NAME, $this->_("Task name"), false);
        $this->addViewColumn(Gpf_Db_Table_Tasks::PROGRESS_MESSAGE, $this->_("Progress"), false);
        $this->addViewColumn('actions', $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Tasks::ID);
        $this->addDataColumn(Gpf_Db_Table_Tasks::DATECREATED, Gpf_Db_Table_Tasks::DATECREATED);
        $this->addDataColumn(Gpf_Db_Table_Tasks::DATECHANGED, Gpf_Db_Table_Tasks::DATECHANGED);
        $this->addDataColumn(Gpf_Db_Table_Tasks::NAME, Gpf_Db_Table_Tasks::NAME);
        $this->addDataColumn(Gpf_Db_Table_Tasks::PROGRESS_MESSAGE, Gpf_Db_Table_Tasks::PROGRESS_MESSAGE);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Tasks::DATECHANGED, '40px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Tasks::DATECREATED, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Tasks::NAME, '100px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Tasks::PROGRESS_MESSAGE, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Tasks::getName());
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
    }

    /**
     * @service tasks read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service tasks export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    /**
     * Delete pending background task if it is allowed
     *
     * @service tasks delete
     * @param $params
     * @return Gpf_Rpc_Action
     */
    public function deleteTask(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);

        $dbTask = new Gpf_Db_Task();
        try {
            $dbTask->setId($action->getParam('taskid'));
            $dbTask->load();
        } catch (Gpf_Exception $e) {
            $action->addError();
            $action->setErrorMessage($this->_('Failed to delete task.'));
            return $action;
        }

        if ($dbTask->isExecuting()) {
            $action->addError();
            $action->setErrorMessage($this->_('It is not possible to delete running task.'));
            return $action;
        }

        try {
            $longTask = Gpf::newObj($dbTask->getClassName());
            if (!$longTask->canUserDeleteTask()) {
                $action->addError();
                $action->setErrorMessage($this->_('This type of task is not allowed to be deleted.'));
                return $action;
            }
        } catch (Gpf_Exception $e) {
        }

        $dbTask->delete();

        $action->addOk();
        $action->setInfoMessage($this->_('Task deleted.'));
        return $action;
    }
}
?>
