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
class Pap_Features_ActionCommission_Stats_Actions extends Pap_Stats_Transactions {

    /**
     * @var Pap_Features_ActionCommission_Stats_ActionsComputer
     */
    private $actionsComputer;
    
    public function __construct(Pap_Stats_Params $params, $commissionTypeId,
        Pap_Features_ActionCommission_Stats_ActionsComputer $computer) {
        parent::__construct($params);
        $this->setCommissionTypeId($commissionTypeId);
        $this->actionsComputer = $computer;
    }
    
    private function initActionsComputer() {
        if (!$this->actionsComputer->isComputed()) {
            $this->actionsComputer->setTier($this->tier);
            $this->actionsComputer->computeStats();
        }
    }
    
    public function getName() {
        $this->initActionsComputer();
        return $this->actionsComputer->getName($this->commissionTypeId);
    }
    
    public function getType() {
        return Pap_Common_Constants::TYPE_ACTION;
    }
    
    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCommission() {
        $this->initActionsComputer();
        return $this->actionsComputer->getCommissions($this->commissionTypeId);
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getTotalCost() {
        $this->initActionsComputer();
        return $this->actionsComputer->getTotalCost($this->commissionTypeId);
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCount() {
        $this->initActionsComputer();
        return $this->actionsComputer->getCount($this->commissionTypeId);
    }
}
?>
