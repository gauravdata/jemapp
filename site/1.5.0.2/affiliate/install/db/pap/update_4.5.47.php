<?php
class pap_update_4_5_47 {
    public function execute() {
        $task = new Gpf_Db_Task();
        $task->setClassName('Pap_Tracking_Visit_Processor');                      
        
        try {
            $task->loadFromData(array(Gpf_Db_Table_Tasks::CLASSNAME));
            $task->setWorkingAreaFrom(0);   
            $task->setWorkingAreaTo(Pap_Tracking_Visit_Processor::MAX_WORKERS_COUNT - 1);
            $task->update(array(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, Gpf_Db_Table_Tasks::WORKING_AREA_TO));
        } catch (Gpf_Exception $e) {}
    }
}

?>
