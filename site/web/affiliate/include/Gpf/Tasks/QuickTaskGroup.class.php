<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Gpf_Tasks_QuickTaskGroup {
    private $tasks = array();
    private $groupId;

    public function __construct() {
        for ($i=0;$i<10;$i++) {
            $this->groupId = substr(md5(uniqid(rand(), true)), 0, 16);

            $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
            $selectBuilder->select->add(Gpf_Db_Table_QuickTasks::GROUPID);
            $selectBuilder->from->add(Gpf_Db_Table_QuickTasks::getName());
            $selectBuilder->where->add(Gpf_Db_Table_QuickTasks::GROUPID,'=',$this->groupId);

            if ($selectBuilder->getAllRows()->getSize() == 0) {
                break;
            }
        }
    }

    public function add(Gpf_Tasks_QuickTask $quickTask) {
        $quickTask->setGroupId($this->groupId);
        $quickTask->update(array(Gpf_Db_Table_QuickTasks::GROUPID));
        $this->tasks[] = $quickTask;
    }
    
    public function addRequest(Gpf_Rpc_Request $request) {
        $quickTask = new Gpf_Tasks_QuickTask($request);
        $this->add($quickTask);
        return $quickTask->getUrl();
    }
}


?>
