<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Reports_QuickReportData extends Pap_Common_ServerTemplatePanel {

    /**
     * @var Pap_Stats_Params
     */
    protected $statsParams;

    protected function getTemplate() {
        return "quick_report_content";
    }

    /**
     * @service quick_stats read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $this->statsParams = $this->getStatsParams();
        $this->statsParams->initFrom($data->getFilters());

        $this->fillData($data, $params);
        return $data;
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParams() {
        return new Pap_Stats_Params();
    }

    protected function fillDataToTemplate(Gpf_Templates_Template $tmpl, Gpf_Rpc_Params $params) {
        $tmpl->assign('sumTransaction', new Pap_Merchants_Reports_QuickReportData_TransactionsSum());

        $tmpl->assign('clicks', new Pap_Stats_Clicks($this->statsParams));

        $tmpl->assign('impressions', new Pap_Stats_Impressions($this->statsParams));

        $tmpl->assign('transactionTypes', new Pap_Stats_TransactionTypeStatsFirstTier($this->statsParams));
        $tmpl->assign('transactionTypesTier', new Pap_Stats_TransactionTypeStatsHigherTiers($this->statsParams));

        return $tmpl;
    }
}

class Pap_Merchants_Reports_QuickReportData_TransactionsSum extends Pap_Stats_Data_Object {
    /**
     * @var Pap_Stats_Data_Commission
     */
    private $commission, $count, $totalCost;

    public function __construct() {
        parent::__construct();
        $this->clear();
    }

    public function clear() {
        $this->commission = new Pap_Stats_Data_Commission();
        $this->count = new Pap_Stats_Data_Commission();
        $this->totalCost = new Pap_Stats_Data_Commission();
    }

    public function add(Pap_Stats_Transactions $transactions) {
        $this->commission->add($transactions->getCommission());
        $this->count->add($transactions->getCount());
        $this->totalCost->add($transactions->getTotalCost());
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCommission() {
        return $this->commission;
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getTotalCost() {
        return $this->totalCost;
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCount() {
        return $this->count;
    }

    protected function getValueNames() {
        return array('count', 'commission', 'totalCost');
    }
}
?>
