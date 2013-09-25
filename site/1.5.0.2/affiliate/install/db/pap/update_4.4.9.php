<?php
class pap_update_4_4_9 {

    private $replace = array(
    	'sales->totalcost->all' => 'sales->totalCost->all',
    	'transactions->all' => 'transactions->commission->all'
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
