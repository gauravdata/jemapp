<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: LogsForm.class.php 24612 2009-06-11 13:28:02Z aharsani $
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
class Gpf_Log_LogsForm extends Gpf_View_FormService {
	const LOG_CRITICAL = 50;
    const LOG_ERROR = 40;
    const LOG_WARNING = 30;
    const LOG_INFO = 20;
    const LOG_DEBUG = 10;

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Log();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Log event");
    }
       
    /**
     *
     * @service log delete
     * @param none
     * @return Gpf_Rpc_Action
     */
    
    public function deleteAllEvents(Gpf_Rpc_Params $params) {
    	$action = new Gpf_Rpc_Action($params);
    	$action->setInfoMessage($this->_("Event(s) are deleted"));
    	$action->setErrorMessage($this->_("No event(s) to delete"));
    	
    	$delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_Logs::getName());
        try {
        	$delete->execute();
        	$action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
        	$action->addError();
        }
        
        return $action;
    }
    
    /**
     *
     * @service log delete
     * @param none
     * @return Gpf_Rpc_Action
     */
    public function deleteDebugEvents(Gpf_Rpc_Params $params) {
    	$action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Debug event(s) are deleted"));
        $action->setErrorMessage($this->_("No debug event(s) to delete"));
    	
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_Logs::getName());
        $delete->where->add(Gpf_Db_Table_Logs::LEVEL, "=", self::LOG_DEBUG);
        try {
            $delete->execute();
            $action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
        	$action->addError();
        }
        
        return $action;
    }
    
    /**
     * @service log read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }
    
    /**
     * @service log add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     * @service log write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service log write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
     /**
     * @service log delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
