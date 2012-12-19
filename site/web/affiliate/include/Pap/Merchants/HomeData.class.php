<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CampaignStatisticsData.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_HomeData extends Pap_Common_ServerTemplatePanel {

    protected function getTemplate() {
        return "home_panel_content";
    }
    
    /**
     *
     * @service traffic_stats read
     * @param $data
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $this->fillData($data, $params);
        return $data;
    }
    
    protected function fillDataToTemplate(Gpf_Templates_Template $tmpl, Gpf_Rpc_Params $params) {
        $todayParams = $this->getStatsParams('T');
        $thisMonthParams = $this->getStatsParams('TM');
        
        $tmpl->assign('todayClicks', new Pap_Stats_Clicks($todayParams));
        $tmpl->assign('todayImpressions', new Pap_Stats_Impressions($todayParams));
        $tmpl->assign('todaySales', new Pap_Stats_Sales($todayParams));
        $tmpl->assign('todayCommissions', new Pap_Stats_Transactions($todayParams));
        $tmpl->assign('todayRefunds', new Pap_Stats_Refunds($todayParams));
        $tmpl->assign('todayChargebacks', new Pap_Stats_Chargebacks($todayParams));
        $tmpl->assign('todayTransactionTypes', new Pap_Stats_TransactionTypeStats($todayParams));
        
        $signupBonus = new Pap_Stats_Transactions($todayParams);
        $signupBonus->setTransactionType(Pap_Db_Transaction::TYPE_SIGNUP_BONUS);
        $tmpl->assign('todaySignupBonus', $signupBonus);
        
        $tmpl->assign('thisMonthClicks', new Pap_Stats_Clicks($thisMonthParams));
        $tmpl->assign('thisMonthImpressions', new Pap_Stats_Impressions($thisMonthParams));
        $tmpl->assign('thisMonthSales', $s = new Pap_Stats_Sales($thisMonthParams));
        $tmpl->assign('thisMonthCommissions', new Pap_Stats_Transactions($thisMonthParams));
        $tmpl->assign('thisMonthRefunds', new Pap_Stats_Refunds($thisMonthParams));
        $tmpl->assign('thisMonthChargebacks', new Pap_Stats_Chargebacks($thisMonthParams));
        $tmpl->assign('thisMonthTransactionTypes', new Pap_Stats_TransactionTypeStats($thisMonthParams));
        
        $signupBonusMonth = new Pap_Stats_Transactions($thisMonthParams);
        $signupBonusMonth->setTransactionType(Pap_Db_Transaction::TYPE_SIGNUP_BONUS);
        $tmpl->assign('thisMonthSignupBonus', $signupBonusMonth);

        if (Gpf_Session::getAuthUser()->hasPrivilege(Pap_Privileges::PENDING_TASK, Pap_Privileges::P_READ)) {
            $pendingTasksGadget = new Pap_Merchants_ApplicationGadgets_PendingTasksGadgets();
            $pendingTasksData = $pendingTasksGadget->load($params);
            $tmpl->assign('pendingTasks', array('affiliates' => $pendingTasksData->getValue('pendingAffiliates'),'links'=>$pendingTasksData->getValue('pendingDirectLinks'),'emails'=>$pendingTasksData->getValue('unsentEmails'),'commissions'=>$pendingTasksData->getValue('pendingCommissions'),'totalcosts'=>$pendingTasksData->getValue('totalCommissions')));
        } else {
            $tmpl->assign('pendingTasks', false);
        }
        
        return $tmpl;
    }
        
    protected function modifyStatsParams(Pap_Stats_Params $statsParams) {
        return $statsParams;
    }
    
    /**
     * @return Pap_Stats_Params
     */
    private function getStatsParams($datePreset) {
        $date = $this->getDateArray($datePreset);
        $statsParams = new Pap_Stats_Params();
        $statsParams->setDateFrom(new Gpf_DateTime($date["dateFrom"]));
        $statsParams->setDateTo(new Gpf_DateTime($date["dateTo"]));
        
        $statsParams = $this->modifyStatsParams($statsParams);

        return $statsParams;
    }
    
    private function getDateArray($datePreset) {
        $filter = new Gpf_SqlBuilder_Filter(array('', 'DP', $datePreset));
        return $filter->addDateValueToArray(array());
    }
}

?>
