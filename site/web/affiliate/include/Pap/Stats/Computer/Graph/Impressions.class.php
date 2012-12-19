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
class Pap_Stats_Computer_Graph_Impressions extends Pap_Stats_Computer_Graph_Base {
    
    public function __construct(Pap_Stats_Params $params, $timeGroupBy) {
        parent::__construct(Pap_Db_Table_Impressions::getInstance(), $params, $timeGroupBy);
    }

    protected function initSelectClause() {
        parent::initSelectClause();
        $this->selectBuilder->select->add('sum(t.'.Pap_Db_Table_Impressions::RAW.')', "value");
    }
}
?>
