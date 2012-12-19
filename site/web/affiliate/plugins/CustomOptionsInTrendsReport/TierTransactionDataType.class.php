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
class CustomOptionsInTrendsReport_TierTransactionDataType extends Pap_Common_Reports_Chart_TransactionDataType {
        
    private $dataType;
    private $tier;
    
    public function __construct($id, $name, $dataType, $tier) {
        parent::__construct($id, $name, $dataType);
        $this->dataType = $dataType;
        $this->tier = $tier;
    }
    
    /**
     * @return Pap_Stats_Computer_Graph_Transactions
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        $computer = new Pap_Stats_Computer_Graph_Transactions($statsParameters, $this->dataType, $timeGroupBy);
        $computer->setTier($this->tier);
        return $computer;
    }
    
}

?>
