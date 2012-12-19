<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class Pap_Features_ActionCommission_Main extends Gpf_Plugins_Handler {

	const COUNT_POSTFIX = '_Count';
	const TOTALCOST_POSTFIX = '_TotalCost';
	const COMMISSIONS_POSTFIX = '_Commissions';
	
    /**
     * @var Pap_Features_ActionCommission_Main
     */
    private static $instance;
    
    private static $userCommissionTypes = array();
    
    /**
     * @var Pap_Common_Banner_Factory
     */
    private $bannerFactory;

    public static function getHandlerInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Pap_Features_ActionCommission_Main();
        }

        return self::$instance;
    }
    
    public function __construct() {
        $this->bannerFactory = new Pap_Common_Banner_Factory();
    }

    public function initDataTypes(Pap_Common_Reports_StatisticsBase $statistics) {
        foreach ($this->getUserCommissionTypes($statistics->getCampaignId()) as $commissionTypeRecord) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->fillFromRecord($commissionTypeRecord);
            $statistics->addDataType(new Pap_Features_ActionCommission_ActionDataType($commissionType, Pap_Stats_Computer_Graph_Transactions::COUNT));
            $statistics->addDataType(new Pap_Features_ActionCommission_ActionDataType($commissionType, Pap_Stats_Computer_Graph_Transactions::COMMISSION));
            $statistics->addDataType(new Pap_Features_ActionCommission_ActionDataType($commissionType, Pap_Stats_Computer_Graph_Transactions::TOTALCOST));
        }
    }
    
    private function getUserCommissionTypes($campaignId = null) {
        $userId = null;
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            $userId = Gpf_Session::getAuthUser()->getPapUserId();
        }
        if (!array_key_exists($campaignId.'_'.$userId, self::$userCommissionTypes)) {
            self::$userCommissionTypes[$campaignId.'_'.$userId] = Pap_Db_Table_CommissionTypes::getInstance()->getAllUserCommissionTypes($campaignId, Pap_Common_Constants::TYPE_ACTION, $userId);
        }
        return self::$userCommissionTypes[$campaignId.'_'.$userId];
    }

    public function initTransactionTypes(Pap_Stats_TransactionTypeStats $transactionTypes) {
        $campaignId = null;
        if ($transactionTypes->getStatParams()->isCampaignIdDefined()) {
            $campaignId = $transactionTypes->getStatParams()->getCampaignId();
        }
        if ($campaignId == null && $transactionTypes->getStatParams()->isBannerIdDefined()) {
            try {
                $banner = $this->bannerFactory->getBanner($transactionTypes->getStatParams()->getBannerId());
                $campaignId = $banner->getCampaignId();
            } catch (Gpf_Exception $e) {
            }
        }
        foreach ($this->getUserCommissionTypes($campaignId) as $commissionType) {
            $transactionTypes->addTransType(Pap_Common_Constants::TYPE_ACTION, $commissionType->get('commtypeid'));
        }
    }
    
    public function initStatsColumns(Pap_Common_StatsColumnsContext $context) {
    	$statColumns = $context->getStatsColumns();
    	foreach ($this->getUserCommissionTypes() as $commissionType) {
    		$action = $this->getActionName($commissionType->get(Pap_Db_Table_CommissionTypes::ID));
			$statColumns[$action . self::COUNT_POSTFIX] = "IF($action.count is NULL, 0, $action.count)";
        	$statColumns[$action . self::TOTALCOST_POSTFIX] = "IF($action.totalcost is NULL, 0, $action.totalcost)";
        	$statColumns[$action . self::COMMISSIONS_POSTFIX] = "IF($action.commission is NULL, 0, $action.commission)";
    	}
    	$context->setStatsColumns($statColumns);
    }
    
    public function buildStatsFrom(Pap_Common_StatsGridParamsContext $context) {
    	$statsGrid = $context->getStatsGrid();
    	$selectBuilder = $context->getSelectBuilder();
    	
    	foreach ($this->getUserCommissionTypes() as $commissionType) {
    		$commissionTypeId = $commissionType->get(Pap_Db_Table_CommissionTypes::ID);
			if ($statsGrid->areColumnsRequired($this->getActionColumns($commissionTypeId))) {
				$action = $this->getActionName($commissionTypeId);
            	$transSelect = $this->getTransactionStatsSelect($context, $commissionTypeId);
            	$selectBuilder->from->addLeftJoin('('.$transSelect->toString().')',
                    $action, $action . '.' . Pap_Common_StatsGrid::GROUP_COLUMN_ALIAS.'='.$statsGrid->getMainTablePrefix(). '.'. $statsGrid->getMainTableColumn());
        	}
    	}
    }
    
    public function addAllActionsViewColumns(Pap_Common_StatsGrid $statsGrid) {
    	foreach ($this->getUserCommissionTypes() as $commissionType) {
    		$action = $this->getActionName($commissionType->get(Pap_Db_Table_CommissionTypes::ID));
    		$statsGrid->addViewColumn($action . self::COUNT_POSTFIX, $this->_("%s count", $commissionType->get('name')));
    		if (!$this->isSubAffSaleStats($statsGrid)) {
    			$statsGrid->addViewColumn($action . self::TOTALCOST_POSTFIX, $this->_("%s total cost", $commissionType->get('name')));
    		}
    		$statsGrid->addViewColumn($action . self::COMMISSIONS_POSTFIX, $this->_("%s commissions", $commissionType->get('name')));
    	}
    }
    
    public function getCustomFilterFields(Gpf_View_CustomFilterFields $filterFields) {
        $filterFields->addStringField(Pap_Db_Table_CommissionTypes::CODE, $this->_("Action code"));
    }

    public function addFilter(Gpf_Plugins_ValueContext $context) {
        $filter = $this->getFilterFromContext($context);
        $where = $this->getWhereFromContext($context);
        $operator = $filter->getRawOperator();
        if($filter->getCode() == Pap_Db_Table_CommissionTypes::CODE) {
            $filter->setCode('ct.' . $filter->getCode());
            $filter->addTo($where);
            if($operator->getCode() == 'NE' || $operator->getCode() == 'NL') {
                $where->add('ct.code', '=', null, 'or');
            }
        }
    }

    /**
     *
     * @return Gpf_SqlBuilder_Filter
     */
    private function getFilterFromContext(Gpf_Plugins_ValueContext $context) {
        $array = $context->getArray();
        return  $array['filter'];
    }
    
    /**
     *
     * @return Gpf_SqlBuilder_WhereClause
     */
    private function getWhereFromContext(Gpf_Plugins_ValueContext $context) {
        $array = $context->getArray();
        return  $array['whereClause'];
    }
    
    private function getActionColumns($commissionTypeID) {
    	$action = $this->getActionName($commissionTypeID);
    	return array($action . self::COUNT_POSTFIX,
					$action . self::TOTALCOST_POSTFIX,
					$action . self::COMMISSIONS_POSTFIX);
    }

    private function getActionName($commissionTypeID) {
    	return 'action_' . $commissionTypeID;
    }
    
    private function getTransactionStatsSelect(Pap_Common_StatsGridParamsContext $context, $commissionTypeId) {
    	$statsGrid = $context->getStatsGrid();
    	$statParams = $context->getStatsParams();
    	if ($this->isSubAffSaleStats($statsGrid)) {
    		$subAffSaleStats = new Pap_Affiliates_Reports_SubAffSaleStatsBuilder($statParams, $statsGrid->getGroupColumn(), Pap_Common_StatsGrid::GROUP_COLUMN_ALIAS);
			$this->addActionConditionsToWhere($subAffSaleStats->getTransactionsWhereClause(), $commissionTypeId);
			return $subAffSaleStats->getStatsSelect();
    	}
    	$actionStats = new Pap_Features_ActionCommission_ActionStatsBuilder($statParams, $statsGrid->getGroupColumn(),
    	Pap_Common_StatsGrid::GROUP_COLUMN_ALIAS, $commissionTypeId);
    	return $actionStats->getStatsSelect();
    }
    
    private function addActionConditionsToWhere(Gpf_SqlBuilder_WhereClause $where, $commissionTypeId, $prefix = '') {
		$where->add($prefix . Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_ACTION);
		$where->add($prefix . Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $commissionTypeId);
    }
    
    private function isSubAffSaleStats(Pap_Common_StatsGrid $statsGrid) {
    	return $statsGrid instanceof Pap_Affiliates_Reports_SubaffSaleStatsGrid;
    }
}
?>
