<?php
class pap_update_4_4_8 {

    private $replace = array('impressionsRaw' => 'impressions->count->raw',
                             'impressionsUnique' => 'impressions->count->unique',

                             'clicksRaw' => 'clicks->count->raw',
                             'clicksUnique' => 'clicks->count->unique',

                             'salesApproved' => 'sales->count->approved',
                             'salesPending' => 'sales->count->pending',
                             'salesDeclined' => 'sales->count->declined',
                             'salesPaid' => 'sales->count->paid',
                             'allCommissionsSales' => 'sales->commission->all',

                             'approvedTotalCostSales' => 'sales->totalCost->approved',
                             'pendingTotalCostSales' => 'sales->totalCost->pending',
                             'declinedTotalCostSales' => 'sales->totalCost->declined',
                             'paidTotalCostSales' => 'sales->totalCost->paid',

                             'allActionsCommissions' => 'actions->commission->all',
                             'allActions' => 'actions->count->all',
                             'allTotalCostActions' => 'actions->totalCost->all',

                             'commissionsApproved' => 'transactions->commission->approved',
                             'commissionsPending' => 'transactions->commission->pending',
                             'commissionsDeclined' => 'transactions->commission->declined',
                             'commissionsPaid' => 'transactions->commission->paid',
     
                             'commissionsApprovedMultitier' => 'transactionsTier->commission->approved',
                             'commissionsPendingMultitier' => 'transactionsTier->commission->pending',
                             'commissionsDeclinedMultitier' => 'transactionsTier->commission->declined',
                             'commissionsPaidMultitier' => 'transactionsTier->commission->paid',
    
    						 //previous update fix
    				
                             'sales->totalcost->approved' => 'sales->totalCost->approved',
                             'sales->totalcost->pending' => 'sales->totalCost->pending',
                             'sales->totalcost->declined' => 'sales->totalCost->declined',
                             'sales->totalcost->paid' => 'sales->totalCost->paid',
                             'actions->totalcost->all' => 'actions->totalCost->all',
    );

    public function execute() {
        $templatesSelect = new Gpf_SqlBuilder_SelectBuilder();
        $templatesSelect->select->addAll(Gpf_Db_Table_MailTemplates::getInstance());
        $templatesSelect->from->add(Gpf_Db_Table_MailTemplates::getName());
        $templatesSelect->where->add(Gpf_Db_Table_MailTemplates::CLASS_NAME, 'IN',
        array('Pap_Mail_Reports_DailyReport', 'Pap_Mail_Reports_WeeklyReport',
        		'Pap_Mail_Reports_MonthlyReport', 'Pap_Mail_Reports_AffDailyReport',
        		'Pap_Mail_Reports_AffMonthlyReport', 'Pap_Mail_Reports_AffWeeklyReport'));

        $t = new Gpf_Db_MailTemplate();
        $templates = $t->loadCollectionFromRecordset($templatesSelect->getAllRows());
        foreach ($templates as $template) {
            $this->processTemplate($template);
        }
    }

    private function processTemplate(Gpf_Db_MailTemplate $template) {
        foreach ($this->replace as $search => $replace) {
            $template->setSubject(str_replace($search, $replace, $template->getSubject()));
            $template->setBodyHtml(str_replace($search, $replace, $template->getBodyHtml()), false);
            $template->setBodyText(str_replace($search, $replace, $template->getBodyText()));
        }
        $template->save();
    }
}
?>
