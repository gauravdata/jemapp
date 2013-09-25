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
class Gpf_Tasks_Scheduler extends Gpf_Object {
    
    public function scheduleTasks($inclusion_type, $inclusion_tasks) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Gpf_Db_Table_PlannedTasks::getInstance());
        $select->from->add(Gpf_Db_Table_PlannedTasks::getName());
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(Gpf_Db_Table_PlannedTasks::LASTPLANDATE, '<', Gpf_Common_DateUtils::now(), 'OR');
        $condition->add(Gpf_Db_Table_PlannedTasks::LASTPLANDATE, 'is', 'NULL', 'OR', false);
        $select->where->addCondition($condition);
        if ($inclusion_type == Gpf_Tasks_Runner::INCLUDE_TASKS) {
            $select->where->add(Gpf_Db_Table_PlannedTasks::CLASSNAME, 'IN', $inclusion_tasks);
        } else if ($inclusion_type == Gpf_Tasks_Runner::EXCLUDE_TASKS) {
            $select->where->add(Gpf_Db_Table_PlannedTasks::CLASSNAME, 'NOT IN', $inclusion_tasks);
        }
        foreach ($select->getAllRows() as $plannedTaskRow) {
            $plannedTask = new Gpf_Db_PlannedTask();
            $plannedTask->fillFromRecord($plannedTaskRow);
            if ($plannedTask->getLastPlanDate() == null) {
                $plannedTask->setLastPlanDate(Gpf_Common_DateUtils::now());
            }            
            $task = new Gpf_Db_Task();
            $task->setClassName($plannedTask->getClassName());
            $task->setParams($plannedTask->getParams());
            $task->setAccountId($plannedTask->getAccountId());
            $task->save();
            
            $preset = new Gpf_Recurrence_Preset();
            $preset->setId($plannedTask->getRecurrencePresetId());
            $preset->load();
            $nextDate = $preset->getNextDate(Gpf_Common_DateUtils::mysqlDateTime2Timestamp($plannedTask->getLastPlanDate()));
            if ($nextDate != null && $nextDate > 0) {
                $plannedTask->setLastPlanDate(Gpf_Common_DateUtils::getDateTime($nextDate));
                $plannedTask->update();                
            }
        }
    }
}

?>
