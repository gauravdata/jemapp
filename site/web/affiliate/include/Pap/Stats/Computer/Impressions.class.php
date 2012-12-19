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
class Pap_Stats_Computer_Impressions extends Pap_Stats_Computer_Base {

    /**
     * @var Pap_Stats_Data_Impression
     */
    private $result;
    
    public function __construct(Pap_Stats_Params $params) {
        parent::__construct(Pap_Db_Table_Impressions::getInstance(), $params);
    }

    protected function initSelectClause() {
        $this->selectBuilder->select->add("sum(t.".Pap_Db_Table_Impressions::RAW.")", "raw");
        $this->selectBuilder->select->add("sum(t.".Pap_Db_Table_Impressions::UNIQUE.")", "uniq");
    }
    
    protected function processResult() {
        try {
            $resultRow = $this->selectBuilder->getOneRow();
            $this->result = new Pap_Stats_Data_Impression($resultRow->get("raw"),
                                                             $resultRow->get("uniq"));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->result = new Pap_Stats_Data_Impression(0, 0);
            return;
        }
    }
    
    /**
     * @return Pap_Stats_Data_Impression
     */
    public function getResult() {
        return $this->result;
    }
}
?>
