<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Stats_Transactions extends Pap_Stats_Base {

    /**
     * @var Pap_Stats_Computer_Transactions
     */
    private $computer = null;
    private $transactionType = null;
    protected $commissionTypeId = null;
    protected $tier = null;
    private $name;

    public function __construct(Pap_Stats_Params $params) {
        parent::__construct($params);
        $this->name = $this->_('All');
    }

    private function init() {
        if ($this->computer != null) {
            return;
        }
        $this->computer = $this->createComputer();
        $this->computer->setTransactionType($this->transactionType);
        $this->computer->setCommissionTypeId($this->commissionTypeId);
        $this->computer->setTier($this->tier);
        $this->computer->computeStats();
    }

    protected function createComputer() {
        return new Pap_Stats_Computer_Transactions($this->params);
    }

    public function setTransactionType($type) {
        $this->transactionType = $type;
        $this->name = Pap_Common_Constants::getTypeAsText($type);
    }

    public function setCommissionTypeId($commTypeId) {
        $this->commissionTypeId = $commTypeId;
        if ($commTypeId != null) {
            $this->loadNameFromCommissionType($commTypeId);
        }
    }

    public function setTier($tier) {
        $this->tier = $tier;
    }

    protected function getValueNames() {
        return array('count', 'commission', 'totalCost', 'refunds', 'chargebacks', 'name', 'type', 'commissiontypeid');
    }

    private static $commissionTypesCache = array();

    /**
     * @return Pap_Db_CommissionType
     */
    private function loadCommissionType($commTypeId) {
        if (!array_key_exists($commTypeId, self::$commissionTypesCache)) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->setId($commTypeId);
            try {
                $commissionType->load();
            } catch (Gpf_Exception $e) {
                $commissionType->setName($this->_('Unknown'));
            }
            self::$commissionTypesCache[$commTypeId] = $commissionType;
        }
        return self::$commissionTypesCache[$commTypeId];
    }

    private function loadNameFromCommissionType($commTypeId) {
        $commissionType = $this->loadCommissionType($commTypeId);
        $this->name = $commissionType->getName();
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->transactionType;
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCommission() {
        $this->init();
        return $this->computer->getCommissions();
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getTotalCost() {
        $this->init();
        return $this->computer->getTotalCost();
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCount() {
        $this->init();
        return $this->computer->getCount();
    }

    /**
     * @return Pap_Stats_Refunds
     */
    public function getRefunds() {
        return $this->initRefundChargeBackClass(new Pap_Stats_Refunds($this->params));
    }

    /**
     * @return Pap_Stats_Chargebacks
     */
    public function getChargebacks() {
        return $this->initRefundChargeBackClass(new Pap_Stats_Chargebacks($this->params));
    }

    private function initRefundChargeBackClass(Pap_Stats_RefundChargeback $refundchargeback) {
        $refundchargeback->setTransactionType($this->transactionType);
        $refundchargeback->setCommissionTypeId($this->commissionTypeId);
        $refundchargeback->setTier($this->tier);
        return $refundchargeback;
    }
    
    public function getCommissionTypeId() {
        return $this->commissionTypeId;
    }
}
?>
