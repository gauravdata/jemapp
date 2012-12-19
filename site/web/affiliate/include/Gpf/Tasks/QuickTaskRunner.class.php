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
class Gpf_Tasks_QuickTaskRunner extends Gpf_Object{

    /**
     *
     * @param $quickTaskId
     * @return Gpf_Db_QuickTask
     * @throws Gpf_DbEngine_NoRowException
     */
    private function getQuickTask($quickTaskId) {
        $quickTask = new Gpf_Db_QuickTask();
        $quickTask->setId($quickTaskId);
        $quickTask->load();
        return $quickTask;
    }

    public function executeTask() {
        $quickTaskId = $_GET['quicktask'];

        if (!$this->existQuickTask($quickTaskId) ) {
            $this->output($this->_('Task does not exist!'));
            return;
        }

        $quickTask = $this->getQuickTask($quickTaskId);
        if (!$quickTask->isValid()) {
            $this->output($this->_('Task is not valid!'));
            return;
        }
        
        try {
            $method = new Gpf_Tasks_QuickTaskRunner_ServiceMethod($quickTask->getRequest());
            $response = $method->execute();
        } catch (Gpf_Exception $e) {
            $this->output($e->getMessage());
            return;
        }
        $this->output($response->toText());
        
        Gpf_Db_Table_QuickTasks::getInstance()->removeTasksAfterExecute($quickTask);
       
    }
    
    private function output($message) {
        $template = new Gpf_Templates_Template('quick_task.stpl');
        $template->assign('message', $message);
        $template->assign('title', Gpf_Application::getInstance()->getName() . ' - ' . $this->_('Merchant'));
        echo $template->getHTML();
    }

    public function existQuickTask($quickTaskId) {
        try {
            $this->getQuickTask($quickTaskId);
        } catch (Gpf_DbEngine_NoRowException $exception) {
            return false;
        }
        return true;
    }
}

class Gpf_Tasks_QuickTaskRunner_ServiceMethod extends Gpf_Rpc_ServiceMethod {
    
    private $params;
    
    public function __construct($request) {
        $json = new Gpf_Rpc_Json();
        $this->params = new Gpf_Rpc_Params($json->decode($request));
        parent::__construct($this->params);
    }
    
    /**
     * @return Gpf_Rpc_Serializable
     */
    public function execute() {
        return $this->invoke($this->params);
    }
    
    protected function checkPermissions(Gpf_Rpc_Params $params) {       
    }
}

?>
