<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Affiliates_Reports_TransactionsGrid extends Pap_Common_Reports_TransactionsGridBase {

    function __construct() {
        parent::__construct();
    }

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_Transactions::TRANSACTION_ID, $this->_("ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commission"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::TOTAL_COST, $this->_("Total cost"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIXED_COST, $this->_("Fixed cost"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('t_'.Pap_Db_Table_Transactions::ORDER_ID, $this->_("Order ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PRODUCT_ID, $this->_("Product ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATE_INSERTED, $this->_("Created"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Campaigns::NAME, $this->_("Campaign Name"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::R_TYPE, $this->_("Type"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::TIER, $this->_("Tier"), true);
        $this->addViewColumn('username', $this->_("Affiliate username"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::R_STATUS, $this->_("Status"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PAYOUT_STATUS, $this->_("Paid"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::IP, $this->_("Ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::REFERER_URL, $this->_("Referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commision"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::RECURRING_COMM_ID, $this->_("Recurring commison id"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $this->_("Payout history id"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::CLICK_COUNT, $this->_("Click count"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, $this->_("First click time"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, $this->_("First click referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_IP, $this->_("First click ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, $this->_("First click data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, $this->_("First click data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_TIME, $this->_("Last click time"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, $this->_("Last click referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_IP, $this->_("Last click ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, $this->_("Last click data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, $this->_("Last click data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA1, $this->_("Extra data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA2, $this->_("Extra data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA3, $this->_("Extra data 3"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA4, $this->_("Extra data 4"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA5, $this->_("Extra data 5"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::MERCHANTNOTE, $this->_("Merchant note"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::CHANNEL, $this->_("Channel"), true);
        $this->addViewColumn('payoutdate', $this->_("Payout date"), true);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::COMMISSION, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::TOTAL_COST, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::FIXED_COST, '', 'N');
        $this->addDefaultViewColumn('t_'.Pap_Db_Table_Transactions::ORDER_ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::PRODUCT_ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::CHANNEL, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::DATE_INSERTED, '', 'D');
        $this->addDefaultViewColumn(Pap_Db_Table_Campaigns::NAME, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::R_TYPE, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::R_STATUS, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::MERCHANTNOTE, '', 'N');

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Affiliates.Transactions.initDefaultView', $this);
    }

    protected function buildWhere() {
        parent::buildWhere();
        $userId = Gpf_Session::getAuthUser()->getPapUserId();
        $this->_selectBuilder->where->add("t.".Pap_Db_Table_Transactions::USER_ID, "=", $userId);
    }

    /**
     * @service transaction read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    protected function loadResultData() {
        $result = $this->initResult();
        foreach ($this->createRowsIterator() as $row) {
            if($row->get(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA) == Gpf::NO){
                $this->setForbiddenFirstClickData($row);
            }
            
            if($row->get(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA) == Gpf::NO){
                $this->setForbiddenLastClickData($row);
            }
            
            $result->add($row);
        }
        return $this->afterExecute($result);
    }

    private function setForbiddenFirstClickData($row){
        $row->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::FIRST_CLICK_IP,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::FIRST_CLICK_TIME,$this->getForbiddenClickRefererText());
    }
    
    private function setForbiddenLastClickData($row){
        $row->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA1,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA2,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::LAST_CLICK_IP,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::LAST_CLICK_REFERER,$this->getForbiddenClickRefererText());
        $row->set(Pap_Db_Table_Transactions::LAST_CLICK_TIME,$this->getForbiddenClickRefererText());
    }
    
    public function getForbiddenClickRefererText(){
        return $this->_("Other affiliate (hidden due privacy)");
    }
    
    
    /**
     * @service transaction export_own
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
