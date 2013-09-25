<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Features_PerformanceRewards_Condition_SalesCountAndRecurringSalesCount extends Pap_Features_PerformanceRewards_Condition_Stat {
    protected function prepareParams() {
        parent::prepareParams();
        $types = array(Pap_Common_Constants::TYPE_RECURRING, Pap_Db_Transaction::TYPE_SALE);
        $this->params->setType($types);
    }
    
    protected function computeValue() {
        return $this->getStatComputer()->getCount()->getAll();
    }
    
    public function getString() {
        return self::toString();
    }
    
    public static function toString() {
        return Gpf_Lang::_("count of sales and recurring sales");
    }
}
?>
