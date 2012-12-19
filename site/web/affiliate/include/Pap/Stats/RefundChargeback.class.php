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
abstract class Pap_Stats_RefundChargeback extends Pap_Stats_Transactions {
    
    protected function createComputer() {
        return new Pap_Stats_Computer_RefundChargeBack($this->params, $this->getMainType());
    }
    
    public function getName() {
        return Pap_Common_Constants::getTypeAsText($this->getMainType()) . ' of ' . parent::getName();
    }
    
    abstract protected function getMainType();
}
?>
