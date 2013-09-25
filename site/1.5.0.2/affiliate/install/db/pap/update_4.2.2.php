<?php

class pap_update_4_2_2 {
    public function execute() {
        $this->addTemplate(new Pap_Mail_Reports_DailyReport());
        $this->addTemplate(new Pap_Mail_Reports_WeeklyReport());
        $this->addTemplate(new Pap_Mail_Reports_MonthlyReport());
        $this->addTemplate(new Pap_Mail_Reports_AffDailyReport());
        $this->addTemplate(new Pap_Mail_Reports_AffWeeklyReport());
        $this->addTemplate(new Pap_Mail_Reports_AffMonthlyReport());
    }
    
    private function addTemplate(Gpf_Mail_Template $mailTemplate) {
    	$mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
