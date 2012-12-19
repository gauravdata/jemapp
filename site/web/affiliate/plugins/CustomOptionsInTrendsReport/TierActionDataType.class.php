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
class CustomOptionsInTrendsReport_TierActionDataType extends Pap_Common_Reports_Chart_TransactionDataType {

    /**
     * @var Pap_Db_CommissionType
     */
    private $commissionType;
    private $tier;
    const DATATYPE = Pap_Stats_Computer_Graph_Transactions::COUNT;

    public function __construct(Pap_Db_CommissionType $commissionType, $tier) {
        parent::__construct($commissionType->getId() . self::DATATYPE . $tier,
            $this->getNameFrom($commissionType, $tier),
            self::DATATYPE, Pap_Common_Constants::TYPE_ACTION);
        $this->commissionType = $commissionType;
        $this->tier = $tier;
    }

    private function getNameFrom(Pap_Db_CommissionType $commissionType, $tier) {
        return $this->_('Tier %s - %s', $tier, $commissionType->getName());
    }

    /**
     * @return Pap_Stats_Computer_Graph_Base
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        $computer = parent::getComputer($statsParameters, $timeGroupBy);
        $computer->setCommissionTypeId($this->commissionType->getId());
        $computer->setTier($this->tier);
        return $computer;
    }
}

?>
