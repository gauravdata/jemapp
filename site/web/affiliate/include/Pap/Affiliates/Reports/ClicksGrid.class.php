<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TransactionReportsGrid.class.php 16621 2008-03-21 09:37:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Affiliates_Reports_ClicksGrid extends Pap_Merchants_Reports_ClicksGrid {

    function __construct() {
        parent::__construct();
    }

    protected function initViewColumns() {
        //Not sortable columns (optimized for speed)
        $this->addViewColumn(Pap_Db_Table_RawClicks::ID, $this->_("ID"), true);
        $this->addViewColumn('banner', $this->_("Banner"), false);
        $this->addViewColumn("campaign", $this->_("Campaign"), false);

        $this->addViewColumn(Pap_Db_Table_RawClicks::DATETIME, $this->_("Date"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_RawClicks::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn('channelname', $this->_("Channel"), true);
        $this->addViewColumn(Pap_Db_Table_RawClicks::DATA1, $this->_("Extra data 1"), true);
        $this->addViewColumn(Pap_Db_Table_RawClicks::DATA2, $this->_("Extra data 2"), true);
        $this->addViewColumn(Pap_Db_Table_RawClicks::REFERERURL, $this->_("Referer URL"));
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_RawClicks::ID, '40px', 'D');
        $this->addDefaultViewColumn('banner', '40px');
        $this->addDefaultViewColumn("campaign", '40px');
        $this->addDefaultViewColumn('channelname', '40px');
        $this->addDefaultViewColumn(Pap_Db_Table_RawClicks::DATETIME, '40px');
        $this->addDefaultViewColumn(Pap_Db_Table_RawClicks::IP, '40px');
        $this->addDefaultViewColumn(Pap_Db_Table_RawClicks::REFERERURL, '100px');
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->rawClicksSelect->where->add(Pap_Db_Table_RawClicks::USERID, "=", Gpf_Session::getAuthUser()->getPapUserId());
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        parent::addFilter($filter);
        if ($filter->getCode() == 'channelid') {
            $this->rawClicksSelect->where->add(Pap_Db_Table_RawClicks::CHANNEL, "=", $filter->getValue());
        }
    }

    /**
     * @service click read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service click read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        return parent::getCustomFilterFields($params);
    }

    /**
     * @service click export_own
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
