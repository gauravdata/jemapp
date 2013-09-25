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
class Pap_Stats_TransactionTypeStatsHigherTiers extends Pap_Stats_TransactionTypeStats {
    
    protected function initComputer(Pap_Stats_Transactions $computer) {
        parent::initComputer($computer);
        $computer->setTier(Pap_Stats_Computer_Transactions::HIGHER_TIERS);
    }
}
?>
