<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: ClicksImpressions.class.php 27656 2010-03-30 11:11:46Z mkendera $
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
abstract class Pap_Db_Table_ClicksImpressions extends Gpf_DbEngine_Table implements Pap_Stats_Table {
    const RAW = "raw";
    const UNIQUE = "uniq";

    protected function initColumns() {
        $this->createColumn(Pap_Stats_Table::ACCOUNTID, self::CHAR, 8);
        $this->createColumn(Pap_Stats_Table::USERID, self::CHAR, 8);
        $this->createColumn(Pap_Stats_Table::CAMPAIGNID, self::CHAR, 8);
        $this->createColumn(Pap_Stats_Table::BANNERID, self::CHAR, 8);
        $this->createColumn(Pap_Stats_Table::PARENTBANNERID, self::CHAR, 8);
        $this->createColumn(Pap_Stats_Table::COUNTRYCODE, self::CHAR, 2);
        $this->createColumn(Pap_Stats_Table::CDATA1, self::CHAR, 255);
        $this->createColumn(Pap_Stats_Table::CDATA2, self::CHAR, 255);
        $this->createColumn(Pap_Stats_Table::CHANNEL, self::CHAR, 10);
        $this->createColumn(Pap_Stats_Table::DATEINSERTED, self::DATETIME);
        $this->createColumn(self::RAW, self::INT);
        $this->createColumn(self::UNIQUE, self::INT);
    }
    
    /**
     * @return Gpf_SqlBuilder_UnionBuilder
     */
    public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add($groupColumn, $groupColumnAlias);
        $this->initStatsSelect($select->select);
        $select->from->add($this->name());
        $statParams->addTo($select);
        $select->groupBy->add($groupColumn);

        $unionBuilder = new Gpf_SqlBuilder_UnionBuilder();
        $unionBuilder->addSelect($select);
        $statsSelectContext = new Pap_Stats_StatsSelectContext($unionBuilder, $select, $groupColumn, $groupColumnAlias);

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Db_Table_ClicksImpressions.getStatsSelect', $statsSelectContext);
        return $unionBuilder;
    }
    
    protected function initStatsSelect(Gpf_SqlBuilder_SelectClause  $select) {
        $select->add('sum('.self::RAW.')', self::RAW);
        $select->add('sum('.self::UNIQUE.')', self::UNIQUE);
    }
}

?>
