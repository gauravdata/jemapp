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
class Pap_Features_PerformanceRewards_RulesGrid extends Gpf_View_GridService {

	protected function initViewColumns() {
		$this->addViewColumn('number', $this->_("Number"), true);
		$this->addViewColumn('rule', $this->_("Rule"), true);
		$this->addViewColumn(parent::ACTIONS, $this->_("Actions"), false);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn('r.'.Pap_Db_Table_Rules::ID);
		$this->addDataColumn('number', 'r.'.Pap_Db_Table_Rules::ID);
		$this->addDataColumn('what', 'r.'.Pap_Db_Table_Rules::WHAT);
		$this->addDataColumn('status', 'r.'.Pap_Db_Table_Rules::STATUS);
		$this->addDataColumn('date', 'r.'.Pap_Db_Table_Rules::DATE);
		$this->addDataColumn('since', 'r.'.Pap_Db_Table_Rules::SINCE);
		$this->addDataColumn('equation', 'r.'.Pap_Db_Table_Rules::EQUATION);
		$this->addDataColumn('equationvalue1', 'r.'.Pap_Db_Table_Rules::EQUATION_VALUE_1);
		$this->addDataColumn('equationvalue2', 'r.'.Pap_Db_Table_Rules::EQUATION_VALUE_2);
		$this->addDataColumn('action', 'r.'.Pap_Db_Table_Rules::ACTION);
		$this->addDataColumn('commissiongroup', 'cg.'.Pap_Db_Table_CommissionGroups::NAME);
		$this->addDataColumn('bonustype', 'r.'.Pap_Db_Table_Rules::BONUS_TYPE);
		$this->addDataColumn('bonusvalue', 'r.'.Pap_Db_Table_Rules::BONUS_VALUE);
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn('number', '', 'D');
		$this->addDefaultViewColumn('rule', '', 'N');
		$this->addDefaultViewColumn(parent::ACTIONS, '', 'N');
	}

	protected function buildWhere() {
		parent::buildWhere();
		$this->_selectBuilder->where->add('r.'.Pap_Db_Table_Rules::CAMPAIGN_ID, '=', $this->getCampaignId());
	}

	protected function buildFrom() {
		$this->_selectBuilder->from->add(Pap_Db_Table_Rules::getName(), 'r');
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
		  'c.'.Pap_Db_Table_Campaigns::ID.'=r.'.Pap_Db_Table_Rules::CAMPAIGN_ID);
		$this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
          'r.'.Pap_Db_Table_Rules::COMMISSION_GROUP_ID.'=cg.'.Pap_Db_Table_CommissionGroups::ID);
	}

	protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
		$outPutResult = new Gpf_Data_RecordSet();
		$outPutResult->setHeader(array(parent::KEY_COLUMN_ID, 'number', 'rule'));

		foreach ($inputResult as $row) {
		    $rule = new Pap_Features_PerformanceRewards_Rule_Transaction();
		    $rule->fillFromRecord($row);
		    $rule->setCommissionGroupId($row->get('commissiongroup'));
		    try {
			     $outPutResult->add(array($row->get(parent::KEY_COLUMN_ID), $row->get('number'), $rule->getString()));
		    } catch (Pap_Features_PerformanceRewards_UnknownRuleException $e) {
		         $outPutResult->add(array($row->get(parent::KEY_COLUMN_ID), $row->get('number'), $this->_('Unknown rule (%s) - probably created with some feature or plugin - this rule will not be applied.', $rule->getAction())));
		    }
		}
	    
		return $outPutResult;
	}

	private function getCampaignId() {
		if ($this->_params->exists('campaignid')) {
			return $this->_params->get('campaignid');
		}

		throw new Gpf_Exception($this->_('Campaign id is missing'));
	}

	/**
	 * @service rule read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}
}
?>
