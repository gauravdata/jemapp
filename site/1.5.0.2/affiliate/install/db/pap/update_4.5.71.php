<?php
class pap_update_4_5_71 {

    public function execute() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$commissions->list\',\'$commissionsList->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$commissions->list\',\'$commissionsList->list\')');
        $update->execute();
        
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$commissions->list\',\'$commissionsList->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$actions->list\',\'$actionsList->list\')');
        $update->execute();
        
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$sales->list\',\'$salesList->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$sales->list\',\'$salesList->list\')');
        $update->execute();
    }
}
?>
