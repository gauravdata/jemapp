<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class gpf_update_1_1_10 {
    public function execute() {
        $task = new Gpf_Db_Task();
        $task->setClassName('Gpf_Mail_OutboxRunner');
        $task->loadFromData(array(Gpf_Db_Table_Tasks::CLASSNAME));
        
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_MailOutbox::ID);
        $select->from->add(Gpf_Db_Table_MailOutbox::getName());
        $select->orderBy->add(Gpf_Db_Table_MailOutbox::ID);
        $select->limit->set(0,1);
        try {
        	$row = $select->getOneRow();
        	$begining = $row->get(Gpf_Db_Table_MailOutbox::ID);
        } catch (Gpf_Exception $e) {
        	$begining = 1;
        }
        
        try {
            $task->load();
            $task->setParams($begining);
            $task->setWorkingAreaFrom($begining);   
            $task->setWorkingAreaTo(Gpf_Mail_OutboxRunner::MAX_MAIL_WORKERS_COUNT);
            $task->update(array(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, Gpf_Db_Table_Tasks::WORKING_AREA_TO, Gpf_Db_Table_Tasks::PARAMS));
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception('Cannot update Gpf_Mail_OutboxRunner task to paralel version');
        }
    }
}
?>
