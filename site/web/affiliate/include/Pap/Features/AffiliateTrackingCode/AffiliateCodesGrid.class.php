<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 24362 2009-05-11 14:49:32Z jsimon $
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
class Pap_Features_AffiliateTrackingCode_AffiliateCodesGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {


    protected function initViewColumns() {
        $this->addViewColumn('actionName', $this->_("Action"), true);
        $this->addViewColumn('code', $this->_("Javascript tracking code"), true);
        $this->addViewColumn('status', $this->_("Status"), true);
        $this->addViewColumn('note', $this->_("Note"), true);
    }


    protected function initDataColumns() {
        $this->setKeyDataColumn('c.'.Pap_Db_Table_AffiliateTrackingCodes::ID);
        $this->addDataColumn('actionName', 'ct.'.Pap_Db_Table_CommissionTypes::NAME);
        $this->addDataColumn('commType', 'ct.'.Pap_Db_Table_CommissionTypes::TYPE);
        $this->addDataColumn('commTypeId', 'ct.'.Pap_Db_Table_CommissionTypes::ID);
        $this->addDataColumn('code', 'c.'.Pap_Db_Table_AffiliateTrackingCodes::CODE);
        $this->addDataColumn('status', 'c.'.Pap_Db_Table_AffiliateTrackingCodes::R_STATUS);
        $this->addDataColumn(Pap_Db_Table_AffiliateTrackingCodes::TYPE, 'c.'.Pap_Db_Table_AffiliateTrackingCodes::TYPE);
        $this->addDataColumn('note', 'c.'.Pap_Db_Table_AffiliateTrackingCodes::NOTE);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('actionName', 60, 'N');
        $this->addDefaultViewColumn('code', 120, 'N');
        $this->addDefaultViewColumn('status', 30, 'A');
        $this->addDefaultViewColumn('note', 90, 'N');
    }
    
    protected function buildFrom() {
        $affiliateWhere = new Gpf_SqlBuilder_CompoundWhereCondition();
        $affiliateWhere->add(Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID, '=', Gpf_Session::getAuthUser()->getPapUserId(), 'OR');
        $affiliateWhere->add(Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID, 'IS', 'NULL', 'OR', false);
        
        $this->_selectBuilder->from->add(Pap_Db_Table_CommissionTypes::getName(), 'ct');
        $onCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $onCondition->add('ct.'.Pap_Db_Table_CommissionTypes::ID, '=', 'c.'.Pap_Db_Table_AffiliateTrackingCodes::COMMTYPEID, 'AND', false);
        $onCondition->addCondition($affiliateWhere); 
        
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_AffiliateTrackingCodes::getName(), 'c', 
            $onCondition->toString());
    }
    
    protected function buildWhere() {
        $this->_selectBuilder->where->add('ct.'.Pap_Db_Table_CommissionTypes::CAMPAIGNID, '=', $this->_params->get('campaignId'));
        $this->_selectBuilder->where->add('ct.'.Pap_Db_Table_CommissionTypes::TYPE, 'IN', array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION));
        $this->_selectBuilder->where->add('ct.'.Pap_Db_Table_CommissionTypes::STATUS, '=', Pap_Db_CommissionType::STATUS_ENABLED);
    }
    
    public function filterRow(Gpf_Data_Row $row) {
        if ($row->get('commType') == Pap_Common_Constants::TYPE_SALE) {
            $row->set('actionName', $this->_('per Sale'));
        }
        if ($row->get(self::KEY_COLUMN_ID) == null) {
            $row->set(self::KEY_COLUMN_ID, "NEW_".$row->get('commTypeId'));
        } 
        return $row;
    }

    /**
     * @service affiliate_tracking_code read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service affiliate_tracking_code read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }
}
?>
