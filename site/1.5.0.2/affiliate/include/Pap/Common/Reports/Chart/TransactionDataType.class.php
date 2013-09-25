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
class Pap_Common_Reports_Chart_TransactionDataType extends Pap_Common_Reports_Chart_BaseDataType {
        
    private $dataType;
    private $transactionType;
    
    public function __construct($id, $name, $dataType, $transactionType = '') {
        parent::__construct($id, $name);
        $this->dataType = $dataType;
        $this->transactionType = $transactionType;
    }
    
    /**
     * @return Pap_Stats_Computer_Graph_Transactions
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        $computer = new Pap_Stats_Computer_Graph_Transactions($statsParameters, $this->dataType, $timeGroupBy);
        $computer->setType($this->transactionType);
        return $computer;
    }
    
    public function getTooltip() {
        if ($this->dataType == Pap_Stats_Computer_Graph_Transactions::COUNT) {
            return parent::getTooltip();
        }
        return '#x_label#<br>'.$this->getName().': '.Pap_Common_Utils_CurrencyUtils::stringToCurrencyFormat('#val#');
    }
}

?>
