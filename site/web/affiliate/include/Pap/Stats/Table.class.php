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
interface Pap_Stats_Table {
    const ACCOUNTID = 'accountid';
    const USERID = 'userid';
    const CAMPAIGNID = 'campaignid';
    const BANNERID = 'bannerid';
    const PARENTBANNERID = 'parentbannerid';
    const COUNTRYCODE = 'countrycode';
    const CDATA1 = 'cdata1';
    const CDATA2 = 'cdata2';
    const CHANNEL = 'channel';
    const DATEINSERTED = 'dateinserted';
    const DATEAPPROVED = 'dateapproved';
    const ORDERID = 'orderid';
    
    public function name();
    
    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias);
}
?>
