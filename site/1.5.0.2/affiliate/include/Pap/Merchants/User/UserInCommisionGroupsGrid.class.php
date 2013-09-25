<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_User_UserInCommisionGroupsGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_UserInCommissionGroup::ID, $this->_("ID"), true);
        $this->addViewColumn("campaign", $this->_("Campaign"), true);
        $this->addViewColumn("commisionGroup", $this->_("Commission group"), true);
        $this->addViewColumn(Pap_Db_Table_UserInCommissionGroup::STATUS, $this->_("Status"), true);
        $this->addViewColumn(Pap_Db_Table_UserInCommissionGroup::NOTE, $this->_("Note"), true);
        $this->addViewColumn(Pap_Db_Table_UserInCommissionGroup::DATE_ADDED, $this->_("Date"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_UserInCommissionGroup::ID);
        $this->addDataColumn(Pap_Db_Table_UserInCommissionGroup::USER_ID, "uicg.".Pap_Db_Table_UserInCommissionGroup::USER_ID);
        $this->addDataColumn("campaign", "c.".Pap_Db_Table_Campaigns::NAME);
        $this->addDataColumn("commisionGroup", "cg.".Pap_Db_Table_CommissionGroups::NAME);
        $this->addDataColumn(Pap_Db_Table_UserInCommissionGroup::STATUS, "uicg.".Pap_Db_Table_UserInCommissionGroup::STATUS);
        $this->addDataColumn(Pap_Db_Table_UserInCommissionGroup::NOTE, "uicg.".Pap_Db_Table_UserInCommissionGroup::NOTE);
        $this->addDataColumn(Pap_Db_Table_UserInCommissionGroup::DATE_ADDED, "uicg.".Pap_Db_Table_UserInCommissionGroup::DATE_ADDED);
    }
    
    protected function initDefaultView() {
    	$this->addDefaultViewColumn("campaign",'', 'D');
        $this->addDefaultViewColumn("commisionGroup",'', 'D');
        $this->addDefaultViewColumn(Pap_Db_Table_UserInCommissionGroup::STATUS, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_UserInCommissionGroup::NOTE, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_UserInCommissionGroup::DATE_ADDED, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), "uicg");
        $onCondition = "uicg.".Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID." = cg.".Pap_Db_Table_CommissionGroups::ID;
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), "cg", $onCondition);
        $onCondition = "cg.".Pap_Db_Table_CommissionGroups::CAMPAIGN_ID. " = c." .Pap_Db_Table_Campaigns::ID;
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), "c", $onCondition);
    }
    
    /**
     * @service user_comm_group read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service user_comm_group export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
