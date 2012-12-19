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
class Pap_Merchants_Payout_PayAffiliatesGrid extends Pap_Merchants_Payout_PayoutGridBase implements Gpf_View_Grid_HasRowFilter {

    const SUBSELECT_TANSACTIONS_TABLE_ALIAS = 't';
    const TANSACTIONS_TABLE_ALIAS = 't';
    
    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    protected $subselect;

	/**
	 * @service pay_affiliate read
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	/**
	 * @service pay_affiliate read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRowCount(Gpf_Rpc_Params $params) {
		return parent::getRowCount($params);
	}

	/**
	 * @service pay_affiliate export
	 * @return Gpf_Rpc_Serializable
	 */
	public function getCSVFile(Gpf_Rpc_Params $params) {
		return parent::getCSVFile($params);
	}

	protected function initViewColumns() {
		$this->addViewColumn(Pap_Db_Table_Transactions::USER_ID, $this->_("User id", true));
		$this->addViewColumn("name", $this->_("Name"), true);
		$this->addViewColumn("firstname", $this->_("First name"), true);
		$this->addViewColumn("lastname", $this->_("Last name"), true);
		$this->addViewColumn("username", $this->_("Email"), true);
		$this->addUserAdditionalViewColumns();

		$supportVat = Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
		if($supportVat == Gpf::YES) {
			$this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Approved"), true);
			$this->addViewColumn("vat", $this->_("VAT"), true);
			$this->addViewColumn("amounttopay", $this->_("To pay"), true);
		} else {
			$this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("To pay"), true);
		}

		$this->addViewColumn("pendingAmount", $this->_("Pending"), true);
		$this->addViewColumn("declinedAmount", $this->_("Declined"), true);
		$this->addViewColumn(Pap_Db_Table_Users::MINIMUM_PAYOUT, $this->_("Minimum payout"), true);
		$this->addViewColumn("payoutMethod", $this->_("Payout method"), true);
		$this->addViewColumn("payoutData", $this->_("Payout data"), true);

		//stats columns
        $this->addViewColumn('salesCount', $this->_("Sales"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('salesTotal', $this->_("Total cost of Sales"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('commissions', $this->_("Commissions of Sales"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('clicksRaw', $this->_("Raw clicks"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('clicksUnique', $this->_("Unique clicks"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('impressionsRaw', $this->_("Raw impressions"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('impressionsUnique', $this->_("Unique impressions"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('ctrRaw', $this->_("Clickthrough ratio"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn('scrRaw', $this->_("Conversion ratio"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn('avgCommissionPerClick', $this->_("Avg. com. per click"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('avgCommissionPerImp', $this->_("Avg. com. per impression"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('avgAmountOfOrder', $this->_("Avg. amount of order"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);

        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn(self::TANSACTIONS_TABLE_ALIAS.'.'.Pap_Db_Table_Transactions::USER_ID);
		$this->addDataColumn(Pap_Db_Table_Transactions::USER_ID, self::TANSACTIONS_TABLE_ALIAS.'.'.Pap_Db_Table_Transactions::USER_ID);

		$this->addDataColumn("accountuserid", "gu.accountuserid");
		$this->addDataColumn("username",   "au.username");
		$this->addDataColumn("firstname",   "au.firstname");
		$this->addDataColumn("lastname",   "au.lastname");
		$this->addUserAditionalDataColumns();
		$this->addDataColumn(Pap_Db_Table_Transactions::COMMISSION, self::TANSACTIONS_TABLE_ALIAS.'.'.Pap_Db_Table_Transactions::COMMISSION);
		$this->addDataColumn("pendingAmount", self::TANSACTIONS_TABLE_ALIAS.'.'.'pendingAmount');
		$this->addDataColumn("declinedAmount", self::TANSACTIONS_TABLE_ALIAS.'.'.'declinedAmount');
		$this->addDataColumn(Pap_Db_Table_Users::MINIMUM_PAYOUT, "pu.".Pap_Db_Table_Users::MINIMUM_PAYOUT);
		$this->addDataColumn("payoutMethod",   "IFNULL(po.".Gpf_Db_Table_FieldGroups::NAME.", 'undefined')");
		$this->addDataColumn("dateinserted", self::TANSACTIONS_TABLE_ALIAS . ".".Pap_Db_Table_Transactions::DATE_INSERTED);
		$this->addDataColumn(Gpf_Db_Table_Users::STATUS, "gu.".Gpf_Db_Table_Users::STATUS);
		$this->initStatColumns();
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn("name", '', 'A');
		$this->addDefaultViewColumn("username", '', 'A');
		$this->addDefaultViewColumn(Pap_Db_Table_Transactions::COMMISSION, '', 'N');

		$supportVat = Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME);
		if($supportVat == Gpf::YES) {
			$this->addDefaultViewColumn("vat", '', 'N');
			$this->addDefaultViewColumn("amounttopay", '', 'N');
		}

		$this->addDefaultViewColumn("payoutMethod", '', 'N');
		$this->addDefaultViewColumn("payoutData", '', 'N');
		$this->addDefaultViewColumn(self::ACTIONS, '', 'N');
	}

	function buildFrom() {
	    $this->subselect = new Gpf_SqlBuilder_SelectBuilder();
	    $this->subselect->select->add(Pap_Db_Table_Transactions::USER_ID);
	    $this->subselect->select->add(Pap_Db_Table_Transactions::DATE_INSERTED);
	    $this->subselect->select->add(Pap_Db_Table_Transactions::CAMPAIGN_ID);
	    $this->subselect->select->add("SUM(IF(".Pap_Db_Table_Transactions::R_STATUS." = '".Pap_Common_Constants::STATUS_APPROVED."', ".Pap_Db_Table_Transactions::COMMISSION.", 0))", Pap_Db_Table_Transactions::COMMISSION);
	    $this->subselect->select->add("SUM(IF(".Pap_Db_Table_Transactions::R_STATUS." = '".Pap_Common_Constants::STATUS_PENDING."', ".Pap_Db_Table_Transactions::COMMISSION.", 0))", "pendingAmount");
        $this->subselect->select->add("SUM(IF(".Pap_Db_Table_Transactions::R_STATUS." = '".Pap_Common_Constants::STATUS_DECLINED."', ".Pap_Db_Table_Transactions::COMMISSION.", 0))", "declinedAmount");
	    $this->subselect->from->add(Pap_Db_Table_Transactions::getName(), self::SUBSELECT_TANSACTIONS_TABLE_ALIAS);
	    $this->subselect->where->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', Pap_Common_Transaction::PAYOUT_UNPAID);
		$this->subselect->groupBy->add(Pap_Db_Table_Transactions::USER_ID);

		$this->_selectBuilder->from->addSubselect($this->subselect, self::TANSACTIONS_TABLE_ALIAS);
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", self::TANSACTIONS_TABLE_ALIAS.".userid = pu.userid");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
		$this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_FieldGroups::getName(), "po", "pu.payoutoptionid = po.fieldgroupid");
		$this->buildStatsFrom();
	}

/**
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField(self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data1", $this->_("Transaction data1"));
        $filterFields->addStringField(self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data2", $this->_("Transaction data2"));
        $filterFields->addStringField(self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data3", $this->_("Transaction data3"));
        $filterFields->addStringField(self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data4", $this->_("Transaction data4"));
        $filterFields->addStringField(self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data5", $this->_("Transaction data5"));
        $this->addStatCustomFilterFields($filterFields);
        return $filterFields->getRecordSet();
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = parent::getStatsParameters();
        $params->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        return $this->addParamsWithDateRangeFilter($params, 'dateinserted');
    }

	/**
	 * @param $row
	 * @return DataRow or null
	 */
	public function filterRow(Gpf_Data_Row $row) {
		$this->addPayoutData($row);
		$this->addVATData($row);
		$this->addNameData($row);
		return $row;
	}

	/**
	 * @return Gpf_Data_RecordSet
	 */
	protected function initResult() {
		$result = parent::initResult();
		$result->addColumn('payoutData');
		$result->addColumn('vat');
		$result->addColumn('amounttopay');
        $result->addColumn('name');
		return $result;
	}

    /**
     * @param Gpf_Data_Row $row
     */
    private function addNameData(Gpf_Data_Row $row) {
        $row->add("name", $row->get('firstname') . ' ' . $row->get('lastname'));
    }

	/**
	 * @param Gpf_Data_Row $row
	 */
	private function addPayoutData(Gpf_Data_Row $row) {
		$payoutData = '';
		foreach ($this->buildUserPayoutOptions($row)->getAllRows() as $payoutOptionRow) {
			if ($payoutData !== '') {
				$payoutData .= ', ';
			}
			$userPayoutOption = new Pap_Db_UserPayoutOption();
			$userPayoutOption->fillFromRecord($payoutOptionRow);
			$payoutData .= $this->_localize($payoutOptionRow->get("payoutFieldName")).": ".$userPayoutOption->getValue();
		}

		$row->add("payoutData", $payoutData);
	}

	/**
	 * @param Gpf_Data_Row $row
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	private function buildUserPayoutOptions(Gpf_Data_Row $row) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add("userid", Pap_Db_Table_UserPayoutOptions::USERID, "pu");
		$select->select->add("name", "payoutFieldName", "ff");
		$select->select->add(Pap_Db_Table_UserPayoutOptions::VALUE, Pap_Db_Table_UserPayoutOptions::VALUE, "upo");
		$select->select->add(Pap_Db_Table_UserPayoutOptions::FORMFIELDID, Pap_Db_Table_UserPayoutOptions::FORMFIELDID, "upo");
		$select->from->add(Pap_Db_Table_Users::getName(), "pu");
		$select->from->addInnerJoin(Gpf_Db_Table_FormFields::getName(), "ff", "(ff.formid = CONCAT('payout_option_', pu.payoutoptionid))");
		$select->from->addInnerJoin(Pap_Db_Table_UserPayoutOptions::getName(), "upo", "(pu.userid = upo.userid AND ff.formfieldid = upo.formfieldid)");
		$select->where->add("pu.userid", "=", $row->get('userid'));
		return $select;
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
            $row->add('amounttopay', $this->_('N/A'));
            return;
        }

        $currency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();

        $payout = new Pap_Common_Payout($user, $currency, $row->get(Pap_Db_Table_Transactions::COMMISSION), null);

        if (!$payout->getApplyVat()) {
            $row->add('vat', $this->_('N/A'));
            $row->add('amounttopay', $row->get(Pap_Db_Table_Transactions::COMMISSION));
            return;
        }
        $row->add('vat', $payout->getVatPercentage() . ' %');
        $row->add('amounttopay', $payout->getAmountWithVat());
	}

	protected function buildWhere() {
		parent::buildFilter();
		if ($this->filters->getSize() == 0) {
			$this->_selectBuilder->where->add('gu.'.Gpf_Db_Table_Users::STATUS, '=', Pap_Common_Constants::STATUS_APPROVED);
		}
        $this->moveTransactionsConditionsToSubselect();
	}

	private function moveTransactionsConditionsToSubselect() {
	    $where = clone $this->_selectBuilder->where;	    
	    $this->_selectBuilder->where = new Gpf_SqlBuilder_WhereClause();	    
	    foreach ($where->getClause() as $clause) {	        
	        if (in_array(self::TANSACTIONS_TABLE_ALIAS, $clause['obj']->getUniqueTablePreffixes())) {
	            $this->subselect->where->addCondition($clause['obj'], $clause['operator']);
	        } else {
	            $this->_selectBuilder->where->addCondition($clause['obj'], $clause['operator']);
	        }
	    }
	}

	protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
		switch ($filter->getCode()) {
			case "search":
				$this->addSearch($filter);
				break;
			case "reachedMinPayout":
				$this->addReachedMinPayout($filter);
				break;
			case "methods":
				$this->addPayoutMethods($filter);
				break;
            case "campaignid":
                $filter->addTo($this->subselect->where);
                break;
            case self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data1":
            case self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data2":
            case self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data3":
            case self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data4":
            case self::SUBSELECT_TANSACTIONS_TABLE_ALIAS.".data5":
                $this->addTransactionDataFilter($filter);
                break;
		}
	}

	private function addSearch(Gpf_SqlBuilder_Filter $filter) {
		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
		$condition->add('au.username', 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add('au.firstname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add('au.lastname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add("po.".Gpf_Db_Table_FieldGroups::NAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add("pu.".Pap_Db_Table_Users::MINIMUM_PAYOUT, "LIKE", "%".$filter->getValue()."%", "OR");
		$condition->add("pu.".Pap_Db_Table_Users::REFID, "LIKE", "%".$filter->getValue()."%", "OR");

		$this->_selectBuilder->where->addCondition($condition);
	}

    protected function initRequiredColumns() {
        parent::initRequiredColumns();
        $this->addRequiredColumn(Pap_Db_Table_Users::MINIMUM_PAYOUT);
        $this->addRequiredColumn('name');
        $this->addRequiredColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addRequiredColumn(Gpf_Db_Table_AuthUsers::LASTNAME);
        $this->addRequiredColumn(Pap_Db_Table_Transactions::COMMISSION);
        $this->addRequiredColumn('rstatus');
        $this->addRequiredColumn('dateinserted');
    }

	private function addReachedMinPayout(Gpf_SqlBuilder_Filter $filter) {
		if ($filter->getValue() != Gpf::YES) {
			return;
		}
		$this->_selectBuilder->having->add(self::TANSACTIONS_TABLE_ALIAS . '.' . Pap_Db_Table_Transactions::COMMISSION, '>=',
                                          'CAST(pu.'.Pap_Db_Table_Users::MINIMUM_PAYOUT.' AS SIGNED)',
                                          'AND', false);
	}

	private function addPayoutMethods(Gpf_SqlBuilder_Filter $filter) {
		$this->_selectBuilder->where->addCondition($this->getPayoutMethodsCondition($filter));
	}

	/**
	 * @param Gpf_SqlBuilder_Filter $filter
	 * @return Gpf_SqlBuilder_CompoundWhereCondition
	 */
	private function getPayoutMethodsCondition(Gpf_SqlBuilder_Filter $filter) {
		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();

		$values = explode(',', $filter->getValue());
		foreach ($values as $value) {
			if ($value === 'null') {
				$condition->add("pu.payoutoptionid", '=', null, 'OR', $filter->getOperator("IN")->getDoQuote());
				$condition->add("pu.payoutoptionid", '=', '', 'OR', $filter->getOperator("IN")->getDoQuote());
				continue;
			}
			$condition->add("pu.payoutoptionid", '=', $value, 'OR', $filter->getOperator("IN")->getDoQuote());
		}
		return $condition;
	}

	/**
     * @param Gpf_SqlBuilder_Filter $filter
     */
	private function addTransactionDataFilter(Gpf_SqlBuilder_Filter $filter) {
	    $filter->addTo($this->subselect->where);
	}

    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes[self::TANSACTIONS_TABLE_ALIAS] = self::TANSACTIONS_TABLE_ALIAS;
        $preffixes['pu'] = 'pu';

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = clone $select->where;
        foreach ($select->having->getClause() as $clause) {
            $count->where->addCondition($clause['obj'], $clause['operator']);
        } 
        $count->groupBy = $select->groupBy;
        return $count;
    }
}
?>
