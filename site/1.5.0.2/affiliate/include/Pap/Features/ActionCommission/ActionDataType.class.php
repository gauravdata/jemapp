<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_ActionCommission_ActionDataType extends Pap_Common_Reports_Chart_TransactionDataType {
        
    /**
     * @var Pap_Db_CommissionType
     */
    private $commissionType;
    
    public function __construct(Pap_Db_CommissionType $commissionType, $dataType) {
        parent::__construct($commissionType->getId() . $dataType, $this->getNameFrom($dataType, $commissionType), $dataType, Pap_Common_Constants::TYPE_ACTION);
        $this->commissionType = $commissionType;
    }
    
    private function getNameFrom($dataType, Pap_Db_CommissionType $commissionType) {
        switch ($dataType) {
            case Pap_Stats_Computer_Graph_Transactions::COUNT:
                return $this->_('Number of %s', $commissionType->getName());
            case Pap_Stats_Computer_Graph_Transactions::COMMISSION:
                return $this->_('Commission of %s', $commissionType->getName());
            case Pap_Stats_Computer_Graph_Transactions::TOTALCOST:
                return $this->_('Revenue of %s', $commissionType->getName());
        }
        return $this->_('Unknown');
    }
    
    /**
     * @return Pap_Stats_Computer_Graph_Base
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        $computer = parent::getComputer($statsParameters, $timeGroupBy);
        $computer->setCommissionTypeId($this->commissionType->getId());
        return $computer;
    }
}

?>
