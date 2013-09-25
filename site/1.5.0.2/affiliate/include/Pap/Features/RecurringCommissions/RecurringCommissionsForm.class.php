<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 21407 2008-10-07 12:54:45Z mbebjak $
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
class Pap_Features_RecurringCommissions_RecurringCommissionsForm extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Features_RecurringCommissions_RecurringCommission();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Recurring commissions");
    }

    /**
     *
     * @service recurring_transaction write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Status of selected recurring transaction(s) was changed"));
        $action->setErrorMessage($this->_("Failed to change status of selected recurring transaction(s)"));

        $ids = array();
        foreach ($action->getIds() as $id) {
            $ids[] = $id;
        }
        
        $status = $params->get("status");

        $this->massUpdateStatus($status, $ids, $action);

        return $action;
    }

    private function massUpdateStatus($status, $ids, $response) {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_RecurringCommissions::STATUS, $status);
        $update->from->add(Pap_Db_Table_RecurringCommissions::getName());

        $update->where->add(Pap_Db_Table_RecurringCommissions::getName().".".Pap_Db_Table_RecurringCommissions::ID, "IN", $ids);

        try {
            $update->execute();
            $response->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $response->addError();
        }
    }

    /**
     * @service recurring_transaction write
     * @param $fields
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service recurring_transaction read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service recurring_transaction add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }

    /**
     * @service recurring_transaction write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }

    /**
     * @service recurring_transaction delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }

    /**
     * @anonym
     * @service
     * @return Gpf_Rpc_Action
     */
    public function createCommissions(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);

        try {
            $this->createCommissionsNoRpc($action->getParam('orderid'), $action->getParam('userid'));
        } catch (Gpf_Exception $e) {
            $action->setErrorMessage($e->getMessage());
            $action->addError();
            return $action;
        }

        $action->setInfoMessage($this->_('Recurring commission processed'));
        $action->addOk();
        return $action;
    }
    
    public function createCommissionsNoRpc($orderId, $userId = null) {
        foreach ($this->loadRecurringCommissionFromOrderId($orderId, $userId) as $recurringCommission) {
            $this->processRecurringCommission($recurringCommission);
        }
    }
    
    protected function processRecurringCommission(Pap_Features_RecurringCommissions_RecurringCommission $recurringCommission) {
        $recurringCommission->createCommissions();
        $recurringCommission->setLastCommissionDate(Gpf_Common_DateUtils::now());
        $recurringCommission->save();
        
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getRecurringCommissions($orderId, $userId = null) {
        return Pap_Features_RecurringCommissions_Main::getRecurringSelect($orderId, $userId)->getAllRows();
    }

    /**
     * @param $orderId
     * @return Gpf_DbEngine_Row_Collection
     * @throws Gpf_Exception
     */
    private function loadRecurringCommissionFromOrderId($orderId, $userId = null) {
        $commissions = $this->getRecurringCommissions($orderId, $userId);

        if ($commissions->getSize() == 0) {
            throw new Gpf_Exception($this->_('Unable to load recurring commission with OrderID %s', $orderId) . (!is_null($userId) ? ' ' .$this->_('and UserId %s', $userId) : '' ));
        }
        
        $recurringCommissions = new Pap_Features_RecurringCommissions_RecurringCommission();
        return $recurringCommissions->loadCollectionFromRecordset($commissions);
    }
}

?>
