<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Transaction.class.php 34218 2011-08-12 17:08:23Z mkendera $
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
class Pap_Common_Transaction extends Pap_Db_Transaction {
    const TRACKING_METHOD_UNKNOWN = 'U';
    const TRACKING_METHOD_3RDPARTY_COOKIE = '3';
    const TRACKING_METHOD_1STPARTY_COOKIE = '1';
    const TRACKING_METHOD_FLASH_COOKIE = 'F';
    const TRACKING_METHOD_FORCED_PARAMETER = 'R';
    const TRACKING_METHOD_IP_ADDRESS = 'I';
    const TRACKING_METHOD_DEFAULT_AFFILIATE = 'D';
    const TRACKING_METHOD_MANUAL_COMMISSION = 'M';
    const TRACKING_METHOD_LIFETIME_REFERRAL = 'L';
    const TRACKING_METHOD_RECURRING_COMMISSION = 'O';
    const TRACKING_METHOD_COUPON = 'C';

    const PAYOUT_PAID = "P";
    const PAYOUT_UNPAID = "U";

    const PAYMENT_PENDING_ID = "toPay";

    protected $originalCurrencyPrecision = 0;

    private $oldStatus;
    private $notification;

    function __construct(){
        parent::__construct();
        $this->setNotification(true);
    }

    public function generateNewTransactionId() {
        $this->generatePrimaryKey();
    }

    public function getNumberOfRecordsFromSameIP($ip, $transType, $periodInSeconds, $parentTransId, $visitDateTime, $campaignId = null, $orderId = null) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add("count(transid)", "count");
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::IP, "=", $ip);
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "=", $transType);
        $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
        if (!is_null($campaignId)) {
            $select->where->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, "=", $campaignId);
        }
        if (!is_null($orderId)) {
            $select->where->add(Pap_Db_Table_Transactions::ORDER_ID, "=", $orderId);
        }
        $dateFrom = new Gpf_DateTime($visitDateTime);
        $dateFrom->addSecond(-1*$periodInSeconds);
        $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, ">", $dateFrom->toDateTime());
        if($parentTransId != null && $parentTransId != '') {
            $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, "<>", $parentTransId);
        }

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->load($select);

        foreach($recordSet as $record) {
            return $record->get("count");
        }
        return 0;
    }

    public function getNumberOfRecordsWithSameOrderId($orderId, $transType, $periodInHours, $parentTransId, $visitDateTime) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add("count(transid)", "count");
        $select->from->add(Pap_Db_Table_Transactions::getName());
        if($orderId == '') {
            $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
            $condition->add(Pap_Db_Table_Transactions::ORDER_ID, '=', '', 'OR');
            $condition->add(Pap_Db_Table_Transactions::ORDER_ID, '=', null, 'OR');
            $select->where->addCondition($condition);
        } else {
            $select->where->add(Pap_Db_Table_Transactions::ORDER_ID, "=", $orderId);
        }
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "=", $transType);
        $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
        $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "<>", Pap_Common_Constants::STATUS_DECLINED);
        if($periodInHours > 0) {
            $dateFrom = new Gpf_DateTime($visitDateTime);
            $dateFrom->addHour(-1*$periodInHours);
            $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, ">", $dateFrom->toDateTime());
        }
        if($parentTransId != null && $parentTransId != '') {
            $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, "<>", $parentTransId);
        }

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->load($select);

        foreach($recordSet as $record) {
            return $record->get("count");
        }
        return 0;
    }

    public static function getTrackingMethodName($type) {
        $obj = new Gpf_Object();
        switch($type) {
            case Pap_Common_Transaction::TRACKING_METHOD_UNKNOWN: return $obj->_('Unknown');
            case Pap_Common_Transaction::TRACKING_METHOD_3RDPARTY_COOKIE: return $obj->_('3rd party cookie');
            case Pap_Common_Transaction::TRACKING_METHOD_1STPARTY_COOKIE: return $obj->_('1st party cookie');
            case Pap_Common_Transaction::TRACKING_METHOD_FLASH_COOKIE: return $obj->_('Flash cookie');
            case Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER: return $obj->_('Forced parameter');
            case Pap_Common_Transaction::TRACKING_METHOD_IP_ADDRESS: return $obj->_('IP address');
            case Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE: return $obj->_('Default affiliate');
            case Pap_Common_Transaction::TRACKING_METHOD_RECURRING_COMMISSION: return $obj->_('Recurring commission');
            default: return $obj->_('Unknown');
        }
    }

    /**
     * @returns Pap_Common_Transaction
     *
     */
    public function getFirstRecordWith($columnName, $value, $status = Pap_Common_Constants::STATUS_DECLINED) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
         
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add($columnName, "=", $value);

        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD));
        $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
        if (is_array($status)) {
            $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "IN", $status);
        }else{
            $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "=", $status);
        }

        $select->limit->set(0, 1);

        $t = new Pap_Common_Transaction();
        $t->fillFromRecord($select->getOneRow());

        return $t;
    }

    /**
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function insert() {
        $this->saveTransaction();
    }

    /**
     * @param array $updateColumns list of columns that should be updated. if not set, all modified columns are update
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function update($updateColumns = array()) {
        $this->saveTransaction($updateColumns);
    }

    protected function saveTransaction($updateColumns = null) {
        $this->updateDateApproved();
        $this->processBeforeSaveExtensionPoint();

        $isNewSale = $this->saveTransactionToDb($updateColumns);

        $this->processAfterSaveExtensionPoint();

        $this->sendNotificationEmails($isNewSale);
    }

    protected function processBeforeSaveExtensionPoint() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Transaction.beforeSave', $this);
    }

    protected function processAfterSaveExtensionPoint() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Transaction.afterSave', $this);
    }

    /**
     * @return bool true if it is new transaction
     */
    protected function saveTransactionToDb($updateColumns) {
        if($updateColumns === null) {
            parent::insert();
            return true;
        }
        parent::update($updateColumns);
        return false;
    }

    /**
     * @param boolean $notification
     */
    public function setNotification($notification) {
        $this->notification = $notification;
    }

    protected function updateDateApproved() {
        if ($this->getStatus() == Pap_Common_Constants::STATUS_APPROVED &&
        ($this->getDateApproved() == null)) {
            $this->setDateApproved(Gpf_Common_DateUtils::now());
        }
    }

    protected function sendNotificationEmails($isNewSale) {
        if (!$this->notification) {
            return;
        }

        if ($this->getType() != Pap_Common_Constants::TYPE_SALE &&
        $this->getType() != Pap_Common_Constants::TYPE_ACTION) {
            return;
        }
        Gpf_Log::debug('SendNotificationEmails started');
        $notification = $this->getTransactionNotificationEmails($this);
        if ($isNewSale) {
            if ($this->getTier() == 1 || $this->getTier() == null) {
                $notification->sendOnNewSaleNotification();
            } else if($this->getTier() == 2) {
                $notification->sendOnNewSaleNotificationToParentAffiliate();
            }
        } else {
            if ($this->oldStatus == $this->getStatus()) {
                Gpf_Log::debug('Notification emails ended. Status not changed.');
                return;
            }
            $notification->sendOnChangeStatusNotification();
        }
        Gpf_Log::debug('SendNotificationEmails ended');
    }

    protected function getTransactionNotificationEmails(Pap_Common_Transaction $transaction) {
        return new Pap_Tracking_Action_SendTransactionNotificationEmails($transaction);
    }

    protected function afterLoad() {
        parent::afterLoad();
        $this->oldStatus = $this->getStatus();
    }

    public function processRefundChargeback($id, $type, $note = '', $orderId = '', $fee = 0, $refundTiers = false) {
        Pap_Contexts_Action::getContextInstance()->debug('Process refund on transaction: '.$id);
        $childTansactions = $this->getTransactionsByParent($refundTiers, $id);    

        $transaction = $this->getTransaction($id);
        try {
            $transaction->refundChargeback($type, $note, $orderId, $fee);
        } catch (Gpf_Exception $e) {
            Pap_Contexts_Action::getContextInstance()->debug($e->getMessage());
        } 
        
        if (!$refundTiers) {
            Pap_Contexts_Action::getContextInstance()->debug('No MultiTier children transactions refunds set');            
            return;
        }      
        
        foreach ($childTansactions as $childTransaction) {
            $this->processRefundChargeback($childTransaction->getId(), $type, $note, $orderId, $fee, true);
        }        
    }
    
    /**
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function getTransactionsByParent($refundTiers, $parentId) {
        if (!$refundTiers) {
            return new Gpf_DbEngine_Row_Collection();
        }
        $transaction = new Pap_Common_Transaction();
        $transaction->setParentTransactionId($parentId);
        return $transaction->loadCollection(array(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID));
    }

    /** 
     * @return Pap_Common_Transaction
     */
    protected function getTransaction($id) {
        $transaction = new Pap_Common_Transaction();
        $transaction->setId($id);
        $transaction->load();
        return $transaction;
    }

    public function refundChargeback($type, $note = '', $orderId = '', $fee = 0) {
        if (($this->getType() == Pap_Common_Constants::TYPE_REFUND)||($this->getType() == Pap_Common_Constants::TYPE_CHARGEBACK)) {
            throw new Gpf_Exception("This transaction is already marked as refund/chargeback!");
        }
        if ($this->getStatus() == Pap_Common_Constants::STATUS_DECLINED) {
            throw new Gpf_Exception("This transaction was declined!");
        }
        if ($this->checkIfHasRefundChargeback() == true ) {
            throw new Gpf_Exception($this->_('Refund or chargeback for this transaction already exists!'));
        }
        $this->addRefundChargeback(new Pap_Db_Transaction(), $type, $note, $orderId, $fee);
    }

    protected function checkIfHasRefundChargeback(){
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Transactions::TRANSACTION_ID, 'id');
        $selectBuilder->from->add(Pap_Db_Table_Transactions::getName());
        $selectBuilder->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '=', $this->getId());
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_REFUND, 'OR');
        $condition->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_CHARGEBACK, 'OR');
        $selectBuilder->where->addCondition($condition);
        $rows = $selectBuilder->getAllRows();
        if ($rows->getSize() > 0) {
            return true;
        }
        return false;

    }

    protected function addRefundChargeback(Pap_Db_Transaction $refundChargeback, $type, $note = '', $orderId = '', $fee = 0) {
        foreach ($this as $name => $value) {
            $refundChargeback->set($name, $value);
        }
        $refundChargeback->setId(Gpf_Common_String::generateId());
        $refundChargeback->setCommission(($this->getCommission() * -1) - $fee);
        $refundChargeback->setType($type);
        if ($orderId != '') {
            $refundChargeback->setOrderId($orderId);
        }
        $refundChargeback->setParentTransactionId($this->getId());
        $refundChargeback->setDateInserted(Gpf_Common_DateUtils::now());
        $refundChargeback->setPayoutStatus(Pap_Common_Constants::PSTATUS_UNPAID);
        $refundChargeback->setMerchantNote($note);
        $refundChargeback->insert();
    }
}

?>
