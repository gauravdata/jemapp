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
class CustomOptionsInTrendsReport_Main extends Gpf_Plugins_Handler {

    /**
     * @var Pap_Features_ActionCommission_Main
     */
    private static $instance;

    public static function getHandlerInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new CustomOptionsInTrendsReport_Main();
        }

        return self::$instance;
    }
    
    public function getDefaultDataType(Gpf_Plugins_ValueContext $value) {
        $value->set('saleTotalCost');
    }

    /**
     * Clicks - Raw
     Clicks - Unique
     Tier 1 - Trial Subscription
     Tier 1 - Recurring Subsription
     Tier 1 - Sales
     Tier 1 - Commissions
     Tier 2 - Trial Subscription
     Tier 2 - Recurring Subsription
     Tier 2 - Sales
     Tier 2 - Commissions
     Grand Total Sales
     Grand Total Commissions
     */
    public function initDataTypes(Pap_Common_Reports_StatisticsBase $statistics) {
        $statistics->clearDataTypes();

        $statistics->addDataType(new Pap_Common_Reports_Chart_ClickDataType($this->_('Clicks - Raw'), Pap_Db_Table_Clicks::RAW));
        $statistics->addDataType(new Pap_Common_Reports_Chart_ClickDataType($this->_('Clicks - Unique'), Pap_Db_Table_Clicks::UNIQUE));

        foreach ($this->getUserCommissionTypes($statistics->getCampaignId()) as $commissionTypeRecord) {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->fillFromRecord($commissionTypeRecord);
            $statistics->addDataType(new CustomOptionsInTrendsReport_TierActionDataType($commissionType, 1));
            $statistics->addDataType(new CustomOptionsInTrendsReport_TierActionDataType($commissionType, 2));
        }

        $statistics->addDataType(new CustomOptionsInTrendsReport_TierTransactionDataType(
            'saleTotalCost1', $this->_('Tier 1 - Sales'), Pap_Stats_Computer_Graph_Transactions::COUNT, 1));
        $statistics->addDataType(new CustomOptionsInTrendsReport_TierTransactionDataType(
            'saleTotalCost2', $this->_('Tier 2 - Sales'), Pap_Stats_Computer_Graph_Transactions::COUNT, 2));
        
        $statistics->addDataType(new CustomOptionsInTrendsReport_TierTransactionDataType(
            'saleCommission1', $this->_('Tier 1 - Commissions'), Pap_Stats_Computer_Graph_Transactions::COMMISSION, 1));
        $statistics->addDataType(new CustomOptionsInTrendsReport_TierTransactionDataType(
            'saleCommission2', $this->_('Tier 2 - Commissions'), Pap_Stats_Computer_Graph_Transactions::COMMISSION, 2));
        
        $statistics->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'saleTotalCost', $this->_('Grand Total Sales'),
            Pap_Stats_Computer_Graph_Transactions::COUNT, Pap_Common_Constants::TYPE_ACTION));
        $statistics->addDataType(new CustomOptionsInTrendsReport_TierTransactionDataType(
            'saleCommission', $this->_('Grand Total Commissions'), Pap_Stats_Computer_Graph_Transactions::COMMISSION, Pap_Stats_Computer_Graph_Transactions::ALL_TIERS));
    }
    
    private function getUserCommissionTypes($campaignId = null) {
        $userId = null;
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            $userId = Gpf_Session::getAuthUser()->getPapUserId();
        }
        return Pap_Db_Table_CommissionTypes::getInstance()->getAllUserCommissionTypes($campaignId, Pap_Common_Constants::TYPE_ACTION, $userId);
    }
}
?>
