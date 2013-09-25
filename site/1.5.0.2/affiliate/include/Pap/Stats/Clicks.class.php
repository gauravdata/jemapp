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
class Pap_Stats_Clicks extends Pap_Stats_Base {
      
    /**
     * @var Pap_Stats_Data_Click
     */
    private $count = null;
    
    /**
     * @var Pap_Stats_Data_Commission
     */
    private $commission = null;
    
    /**
     * @return Pap_Stats_Data_Click
     */
    public function getCount() {
        if ($this->count == null) {
            $computer = new Pap_Stats_Computer_Clicks($this->params);
            $computer->computeStats();
            $this->count = $computer->getResult();
        }
        return $this->count;
    }
    
    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCommission() {
        if ($this->commission == null) {
            $computer = new Pap_Stats_Computer_Transactions($this->params, Pap_Common_Constants::TYPE_CLICK);
            $computer->computeStats();
            $this->commission = $computer->getCommissions();
        }
        return $this->commission;
    }
    
    protected function getValueNames() {
        return array('count', 'commission');
    }
    
}
?>
