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
class Pap_Features_Coupon_CouponsGrid extends Gpf_View_GridService {

    /**
     * @service coupon read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service coupon export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    /**
     * @service coupon read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField('id', $this->_("ID"));
        $filterFields->addStringField('userid', $this->_("User ID"));
        $filterFields->addStringField('couponcode', $this->_("Code"));
        $filterFields->addStringField('rstatus', $this->_("Status"));
        $filterFields->addDateField('valid_from', $this->_("Valid from"));
        $filterFields->addDateField('valid_to', $this->_("Valid to"));
        $filterFields->addNumberField('limit_use', $this->_("Limit use"));
        return $filterFields->getRecordSet();
    }

    protected function initViewColumns() {
        $this->addViewColumn('couponcode', $this->_("Code"), true);
        $this->addViewColumn('userid', $this->_("Affiliate"), true);
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn('valid_from', $this->_("Valid from"), true);
        $this->addViewColumn('valid_to', $this->_("Valid to"), true);
        $this->addViewColumn('limit_use', $this->_("Limit use"), true);
        $this->addViewColumn('sales', $this->_("Sales"), true);
        $this->addViewColumn(parent::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('c.'.Pap_Db_Table_Coupons::ID);
        $this->addDataColumn('couponcode', 'c.'.Pap_Db_Table_Coupons::CODE);
        $this->addDataColumn('userid', 'c.'.Pap_Db_Table_Coupons::USERID);
        $this->addDataColumn('rstatus', 'c.'.Pap_Db_Table_Coupons::STATUS);
        $this->addDataColumn('valid_from', 'c.'.Pap_Db_Table_Coupons::VALID_FROM);
        $this->addDataColumn('valid_to', 'c.'.Pap_Db_Table_Coupons::VALID_TO);
        $this->addDataColumn('limit_use', 'c.'.Pap_Db_Table_Coupons::MAX_USE_COUNT);
        $this->addDataColumn('sales', 'COUNT(t.'.Pap_Db_Table_Transactions::TRANSACTION_ID.')');
        $this->addDataColumn('username', 'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn('firstname', 'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn('lastname', 'au.'.Gpf_Db_Table_AuthUsers::LASTNAME);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('couponcode', '');
        $this->addDefaultViewColumn('userid', '');
        $this->addDefaultViewColumn('rstatus', '');
        $this->addDefaultViewColumn('valid_from', '');
        $this->addDefaultViewColumn('valid_to', '');
        $this->addDefaultViewColumn('limit_use', '');
        $this->addDefaultViewColumn('sales', '');
        $this->addDefaultViewColumn(parent::ACTIONS, '');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Coupons::getName(), 'c');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(),
            'pu', 'pu.'.Pap_Db_Table_Users::ID.'=c.'.Pap_Db_Table_Coupons::USERID);
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Users::getName(),
            'gu', 'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=gu.'.Gpf_Db_Table_Users::ID);
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_AuthUsers::getName(),
            'au', 'au.'.Gpf_Db_Table_AuthUsers::ID.'=gu.'.Gpf_Db_Table_Users::AUTHID);
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Transactions::getName(),
            't', 'c.'.Pap_Db_Table_Coupons::ID.'=t.'.Pap_Db_Table_Transactions::COUPON_ID.' and t.'.Pap_Db_Table_Transactions::TIER.'= 1');        
    }


    protected function buildGroupBy() {
        $this->_selectBuilder->groupBy->add('c.'.Pap_Db_Table_Coupons::ID);
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('c.'.Pap_Db_Table_Coupons::BANNERID, '=', $this->getBannerID());
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        if ($filter->getCode() == 'search') {
            $this->addSearch($filter);
        }
    }

    protected function getBannerID() {
        if ($this->filters->isFilter('bannerid')) {
            return $this->filters->getFilterValue('bannerid');
        }
        throw new Gpf_Exception('Missing banner id');
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('c.'.Pap_Db_Table_Coupons::CODE, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('c.'.Pap_Db_Table_Coupons::VALID_FROM, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('c.'.Pap_Db_Table_Coupons::VALID_TO, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('c.'.Pap_Db_Table_Coupons::MAX_USE_COUNT, 'LIKE', '%'.$filter->getValue().'%', 'OR');

        $this->_selectBuilder->where->addCondition($condition);
    }
}
?>
