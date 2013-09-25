<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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
class ClickStatsOptimizer_Main extends Gpf_Plugins_Handler {

    /**
     * @return ClickStatsOptimizer_Main
     */
    public static function getHandlerInstance() {
        return new ClickStatsOptimizer_Main();
    }

    public function clearDataFields(Pap_Db_ClickImpression $clickImp) {
        $clickImp->setData1('');
        $clickImp->setData2('');
    }
}
?>
