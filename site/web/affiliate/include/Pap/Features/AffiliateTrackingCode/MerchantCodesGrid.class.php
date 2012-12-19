<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_AffiliateTrackingCode_MerchantCodesGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {
	
	protected function initViewColumns() {
        $this->addViewColumn('affiliate', $this->_("Affiliate ID"), true);
        $this->addViewColumn('campaignname', $this->_("Campaign"), true);
        $this->addViewColumn('comtypeName', $this->_("Commission Type"), true);
        
        $this->addViewColumn(Pap_Db_Table_AffiliateTrackingCodes::CODE, $this->_("Tracking Code"), true);
        $this->addViewColumn(Pap_Db_Table_AffiliateTrackingCodes::NOTE, $this->_("Note"), true);
        $this->addViewColumn(Pap_Db_Table_AffiliateTrackingCodes::R_STATUS, $this->_("Status"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Action"), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('atc.'.Pap_Db_Table_AffiliateTrackingCodes::ID);
        $this->addDataColumn('userid', 'atc.'.Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID);
        $this->addDataColumn('username', 'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn('firstname', 'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn('lastname', 'au.'.Gpf_Db_Table_AuthUsers::LASTNAME);
        
        $this->addDataColumn('campaignid', 'cam.'.Pap_Db_Table_Campaigns::ID);
        $this->addDataColumn('campaignname', 'cam.'.Pap_Db_Table_Campaigns::NAME);
        $this->addDataColumn('comtype', 'com.'.Pap_Db_Table_CommissionTypes::TYPE);
        $this->addDataColumn('comtypeName', 'com.'.Pap_Db_Table_CommissionTypes::NAME);
        $this->addDataColumn(Pap_Db_Table_AffiliateTrackingCodes::CODE, 'atc.'.Pap_Db_Table_AffiliateTrackingCodes::CODE);
        $this->addDataColumn(Pap_Db_Table_AffiliateTrackingCodes::NOTE, 'atc.'.Pap_Db_Table_AffiliateTrackingCodes::NOTE);
        $this->addDataColumn(Pap_Db_Table_AffiliateTrackingCodes::R_STATUS, 'atc.'.Pap_Db_Table_AffiliateTrackingCodes::R_STATUS);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('affiliate');
        $this->addDefaultViewColumn('campaignname');
        $this->addDefaultViewColumn('comtypeName');
        $this->addDefaultViewColumn(Pap_Db_Table_AffiliateTrackingCodes::CODE);
        $this->addDefaultViewColumn(Pap_Db_Table_AffiliateTrackingCodes::NOTE);
        $this->addDefaultViewColumn(Pap_Db_Table_AffiliateTrackingCodes::R_STATUS);
        $this->addDefaultViewColumn(self::ACTIONS);
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_AffiliateTrackingCodes::getName(), 'atc');
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionTypes::getName(), 'com', 'com.commtypeid=atc.commtypeid');
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'cam', 'cam.campaignid=com.campaignid');
        
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'u', 'u.userid=atc.userid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'u.accountuserid=gu.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'au.authid=gu.authid');
    }

    protected function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix', 'operator'), array('cam', 'OR'))));
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
            default:
                break;
        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('atc.'.Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('cam.'.Pap_Db_Table_Campaigns::NAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('com.'.Pap_Db_Table_CommissionTypes::NAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('atc.'.Pap_Db_Table_AffiliateTrackingCodes::CODE, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        
        $this->_selectBuilder->where->addCondition($condition);
    }
     
    /**
     * @service affiliate_tracking_code read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service affiliate_tracking_code write
     * @return Gpf_Rpc_Serializable
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    public function filterRow(Gpf_Data_Row $row) {
        if ($row->get('comtype') == Pap_Common_Constants::TYPE_SALE) {
            $row->set('comtypeName', $this->_('per Sale'));
        }
        return $row;
    }
}
?>
