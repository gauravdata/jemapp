<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 21464 2008-10-09 09:09:23Z mbebjak $
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
class Pap_Features_Common_AffiliateGroupGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('username', $this->_("Username"), true);
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn('dateadded', $this->_("Date joined"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('note', $this->_("Note"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn('ucg.'.Pap_Db_Table_UserInCommissionGroup::ID);
        $this->addDataColumn('userid', 'u.'.Pap_Db_Table_Users::ID);
        $this->addDataColumn('firstname', 'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn('lastname', 'au.'.Gpf_Db_Table_AuthUsers::LASTNAME);
        $this->addDataColumn('username', 'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn('rstatus', 'ucg.'.Pap_Db_Table_UserInCommissionGroup::STATUS);
        $this->addDataColumn('dateadded', 'ucg.'.Pap_Db_Table_UserInCommissionGroup::DATE_ADDED);
        $this->addDataColumn('note', 'ucg.'.Pap_Db_Table_UserInCommissionGroup::NOTE);
        $this->addDataColumn('commissiongroupid', 'ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $this->addDataColumn('refid', 'u.'.Pap_Db_Table_Users::REFID);
        $this->addDataColumn('dateinserted', 'u.'.Pap_Db_Table_Users::DATEINSERTED);
        $this->addDataColumn('dateapproved', 'u.'.Pap_Db_Table_Users::DATEAPPROVED);
        for ($i = 1; $i <= 25; $i++) {
            $this->addDataColumn('data'.$i, 'u.data'.$i);
        }
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '', 'D');
        $this->addDefaultViewColumn('username', '', 'D');
        $this->addDefaultViewColumn('rstatus', '', 'N');
        $this->addDefaultViewColumn('dateadded','', 'D');
        $this->addDefaultViewColumn('note','', 'N');
        $this->addDefaultViewColumn(self::ACTIONS,'', 'N');
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('u.rtype', '=', Pap_Application::ROLETYPE_AFFILIATE);
        
        if ($this->_params->exists('commissiongroupid')) {
            $this->_selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, '=', $this->_params->get('commissiongroupid'));
        }
        if ($this->_params->exists('campaignid')) {
            $this->_selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $this->_params->get('campaignid'));
        }
    }

    protected function buildFrom() {
    	$this->_selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
    	$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
    	    'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 
            'u', 'ucg.userid=u.userid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 
            'gu', 'u.accountuserid=gu.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 
            'au', 'au.authid=gu.authid');
    }
    
    protected function buildOrder() {
        if ($this->_sortColumn == "name") {
            $this->_selectBuilder->orderBy->add("au.firstname", $this->_sortAsc);
            $this->_selectBuilder->orderBy->add("au.lastname", $this->_sortAsc);
            return;
        }
        parent::buildOrder();
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
        $condition->add('ucg.userid', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.username', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.firstname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.lastname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        
        $this->_selectBuilder->where->addCondition($condition);
    }
    
    /**
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField("userid", $this->_("Id"));
        $filterFields->addStringField("firstname", $this->_("Firstname"));
        $filterFields->addStringField("lastname", $this->_("Lastname"));
        $filterFields->addStringField("username", $this->_("Username"));
        $filterFields->addDateField("dateadded", $this->_("Date joined"));
        
        return $filterFields->getRecordSet();
    }
    
    /**
     * @service affiliate read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
