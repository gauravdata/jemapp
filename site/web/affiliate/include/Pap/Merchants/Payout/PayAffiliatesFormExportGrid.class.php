<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: PayAffiliatesForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Payout_PayAffiliatesFormExportGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter{

    protected function initViewColumns() {
        $this->addViewColumn('payoutMethodName', $this->_('Payout method'));
        $this->addViewColumn('amount', $this->_('Amount'), false, Gpf_View_ViewColumn::TYPE_CURRENCY);

        $supportVat = Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
        if($supportVat == Gpf::YES) {
            $this->addViewColumn('vat', $this->_('VAT'), false);
            $this->addViewColumn('amountExclVat', $this->_('Amount excl. VAT'), false, Gpf_View_ViewColumn::TYPE_CURRENCY);
            $this->addViewColumn('amountInclVat', $this->_('Amount incl. VAT'), false, Gpf_View_ViewColumn::TYPE_CURRENCY);
        }

        $this->addViewColumn('actions', $this->_('Actions'), false, Gpf_View_ViewColumn::TYPE_CURRENCY);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('pu.'.Pap_Db_Table_Users::PAYOUTOPTION_ID);
        $this->addDataColumn('payoutMethodName', 'po.'.Gpf_Db_Table_FieldGroups::NAME);
        $this->addDataColumn('amount', 'SUM(t.'.Pap_Db_Table_Transactions::COMMISSION.')');
        $this->addDataColumn("accountuserid", "gu.accountuserid");
        $this->addDataColumn("userid", "t.userid");
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('payoutMethodName', '30px');
        $this->addDefaultViewColumn('amount', '10px', 'D');

        $supportVat = Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
        if($supportVat == Gpf::YES) {
            $this->addDefaultViewColumn('vat', '10px', 'D');
            $this->addDefaultViewColumn('amountExclVat', '10px', 'D');
            $this->addDefaultViewColumn('amountInclVat', '10px', 'D');
        }

        $this->addDefaultViewColumn('actions', '5px', 'D');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), 't');
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu', 't.userid = pu.userid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'pu.accountuserid = gu.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_FieldGroups::getName(), 'po', 'pu.payoutoptionid = po.fieldgroupid');
    }

    protected function buildWhere() {
        $this->_selectBuilder->where->add('po.'.Gpf_Db_Table_FieldGroups::DATA2, 'is not', 'NULL', 'AND', false);
        $this->_selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, '=', Pap_Common_Transaction::PAYMENT_PENDING_ID);
    }

    protected function buildGroupBy() {
        $this->_selectBuilder->groupBy->add('pu.'.Pap_Db_Table_Users::PAYOUTOPTION_ID);
    }

    /**
     * @service pay_affiliate read
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = parent::initResult();
        $result->addColumn('vat');
        $result->addColumn('amountExclVat');
        $result->addColumn('amountInclVat');
        return $result;
    }

    protected function getNumberOfNonZeroPaidAffiliates($userIds) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('sum('.Pap_Db_Table_Transactions::COMMISSION.')', 'comsum');
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, '=', Pap_Common_Transaction::PAYMENT_PENDING_ID);
        $select->groupBy->add(Pap_Db_Table_Transactions::USER_ID);
        $select->having->add('comsum', '>', 0);
        return $select->getAllRows()->getSize();
    }

    protected function getUserIdsFromFilter(Gpf_Rpc_Action $action) {
        $userIds = array();
        foreach ($action->getIds() as $id) {
            $userIds[] = $id;
        }
        return $userIds;
    }

    protected function updateSelectedRows(Gpf_Rpc_FilterCollection $filters, $userIds) {
    	$update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, Pap_Common_Transaction::PAYMENT_PENDING_ID);
        $update->from->add(Pap_Db_Table_Transactions::getName());
        $update->where->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, "=", Pap_Common_Transaction::PAYOUT_UNPAID);
        $update->where->add(Pap_Db_Table_Transactions::R_STATUS, '=', Pap_Common_Constants::STATUS_APPROVED);
        $this->addOnlyPossibleFilters($filters, $update->where);
        $update->where->add(Pap_Db_Table_Transactions::USER_ID, 'IN', $userIds);
        
        $result = $update->execute();
        
        return $numRows = $result->affectedRows();;
    }
    
    private function filterIsValid($filterCode, $columnNames) {       
        foreach ($columnNames as $column) {       
            if ($column->getName() == $filterCode) {
                return true;
            }
        }
        return false;
    }
    
    protected function addOnlyPossibleFilters(Gpf_Rpc_FilterCollection $filters,  Gpf_SqlBuilder_WhereClause $where) {
    	$filterIterator = $filters->getIterator();
    	$columns = Pap_Db_Table_Transactions::getInstance()->getColumns();
    	while($filterIterator->valid()) {
    		$filter = $filterIterator->current();
    		
    		if ($this->filterIsValid($filter->getCode(), $columns)) {
    			$filter->addTo($where);
    		}
    		
    		$filterIterator->next();
    	}
    }
    
    /**
     * @service pay_affiliate read
     */
    public function markTransactionsAsPaymentPending(Gpf_Rpc_Params $params) {
        $this->clearPaymentPendingMark();

        $action = new Gpf_Rpc_Action($params);

        $userIds = $this->getUserIdsFromFilter($action);

        $filters = new Gpf_Rpc_FilterCollection($params);

        $filterIterator = $filters->getIterator();

        while($filterIterator->valid()) {
            $filter = $filterIterator->current();
            if(!substr_compare($filter->getCode(), Pap_Merchants_Payout_PayAffiliatesGrid::TANSACTIONS_TABLE_ALIAS.'.data', 0, strlen($filter->getCode())-1)) {
                $suffix = substr($filter->getCode(), strlen($filter->getCode())-1);
                $filter->setCode('data'.$suffix);
            }
            $filterIterator->next();
        }

        $numRows = $this->updateSelectedRows($filters, $userIds);

        if ($numRows == 0) {
            $action->setErrorMessage($this->_('Nobody from selected affiliates have approved commissions. No commissions will be paid.'));
            $action->addError();
            return $action;
        }

        $nonZeroUsersCount = $this->getNumberOfNonZeroPaidAffiliates($userIds);
        if ((count($userIds) - $nonZeroUsersCount) > 0) {
            $action->setErrorMessage($this->_('%s of selected affiliates have no approved or zero commissions. No commissions will be paid to them.', (count($userIds) - $nonZeroUsersCount)));
            $action->addError();
            return $action;
        }

        $action->addOk();

        return $action;
    }

    private function clearPaymentPendingMark() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, 'NULL', false);
        $update->from->add(Pap_Db_Table_Transactions::getName());
        $update->where->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, '=', Pap_Common_Transaction::PAYMENT_PENDING_ID);
        $update->execute();
    }

    public function filterRow(Gpf_Data_Row $row) {
        $this->addVATData($row);
        return $row;
    }

    /**
     * @param Gpf_Data_Row $row
     */
    private function addVATData(Gpf_Data_Row $row) {
        try {
            $user = new Pap_Common_User();
            $user->setId($row->get('userid'));
            $user->load();
        } catch (Gpf_Exception $e) {
            $row->add('vat', $this->_('N/A'));
            $row->add('amountExclVat', $this->_('N/A'));
            $row->add('amountInclVat', $this->_('N/A'));
            return;
        }

        $currency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();

        $payout = new Pap_Common_Payout($user, $currency, $row->get('amount'), null);

        if (!$payout->getApplyVat()) {
            $row->add('vat', $this->_('N/A'));
            $row->add('amountExclVat', $row->get('amount'));
            $row->add('amountInclVat', $row->get('amount'));
            return;
        }
        $row->add('vat', $payout->getVatPercentage() . ' %');
        $row->add('amountExclVat', $payout->getAmountWithoutWat());
        $row->add('amountInclVat', $payout->getAmountWithVat());

    }
}

?>
