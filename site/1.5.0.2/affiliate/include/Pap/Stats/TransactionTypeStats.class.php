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
class Pap_Stats_TransactionTypeStats extends Pap_Stats_Base implements IteratorAggregate {
    /**
     * @var array<Pap_Stats_Transactions>
     */
    private $transTypes = array();
    
    public function __construct(Pap_Stats_Params $params) {
        parent::__construct($params);
        $this->initTransTypes();
    }
    
    protected function getValueNames() {
        return array('types');
    }
    
    private function initTransTypes() {
        $this->addTransType(Pap_Common_Constants::TYPE_CLICK);
        $this->addTransType(Pap_Common_Constants::TYPE_SALE);
        $this->addTransType(Pap_Common_Constants::TYPE_REFUND);
        $this->addTransType(Pap_Common_Constants::TYPE_CHARGEBACK);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Stats.initTransactionTypes', $this);
    }
    
    protected function initComputer(Pap_Stats_Transactions $computer) {
    }
    
    public function addTransType($type, $commTypeId = null) {
        $stats = new Pap_Stats_Transactions($this->params);
        $stats->setTransactionType($type);
        $stats->setCommissionTypeId($commTypeId);
        $this->addTransactionType($stats);
    }
    
    public function addTransactionType(Pap_Stats_Transactions $stats) {
        $this->initComputer($stats);
        $this->transTypes[] = $stats;
    }
    
    /**
     * @return array<Pap_Stats_Transactions>
     */
    public function getTypes() {
        return $this->transTypes;
    }
    
    public function getIterator() {
        return new ArrayIterator($this->transTypes);
    }
    
    /**
     * @return Pap_Stats_Params
     */
    public function getStatParams() {
       return $this->params;
    }
}
?>
