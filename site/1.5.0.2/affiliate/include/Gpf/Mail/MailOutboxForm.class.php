<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: MailOutboxForm.class.php 24980 2009-07-20 10:49:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Gpf_Mail_MailOutboxForm extends Gpf_View_FormService {

	const RESTART_ERROR_MSG = '';
	const RESTART_RETRY_NR = 0;
	const STATUS_PENDING = 'P';
	const STATUS_SEND = 'S';
	
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_MailOutbox();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Mail outbox");
    }
    
    /**
     *
     * @service mail_outbox write
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function restartSendingMail(Gpf_Rpc_Params $params){
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to restart %s row(s)'));
        $action->setInfoMessage($this->_('%s row(s) successfully restarted'));
        
        foreach ($action->getIds() as $id) {
            try {
                $update = new Gpf_SqlBuilder_UpdateBuilder();
                $update->from->add(Gpf_Db_Table_MailOutbox::getName());
                $update->set->add(Gpf_Db_Table_MailOutbox::ERROR_MSG, self::RESTART_ERROR_MSG);
                $update->set->add(Gpf_Db_Table_MailOutbox::RETRY_NR, self::RESTART_RETRY_NR);
                $update->set->add(Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, Gpf_Common_DateUtils::now());
                $update->where->add(Gpf_Db_Table_MailOutbox::ID, '=', $id, 'AND');
                $update->where->add(Gpf_Db_Table_MailOutbox::STATUS, '=', self::STATUS_PENDING);
                $update->executeOne();
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }
    
    /**
     * @service mail_outbox read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }
    
    /**
     * @service mail_outbox add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     * @service mail_outbox write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service mail_outbox write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
     /**
     * @service mail_outbox delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
    
    protected function deleteRow(Gpf_DbEngine_Row $row) {
        $row->load();
        $row->delete();
    }
}

?>
