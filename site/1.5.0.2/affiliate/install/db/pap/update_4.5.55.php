<?php
class pap_update_4_5_55 {

    public function execute() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$commissionsList\',\'$commissions->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$commissionsList\',\'$commissions->list\')');
        $update->execute();
        
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$actionsList\',\'$actions->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$actionsList\',\'$actions->list\')');
        $update->execute();
        
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_MailTemplates::getName());
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_HTML, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_HTML.',\'$salesList\',\'$sales->list\')');
        $update->set->addDontQuote(Gpf_Db_Table_MailTemplates::BODY_TEXT, 'REPLACE ('.Gpf_Db_Table_MailTemplates::BODY_TEXT.',\'$salesList\',\'$sales->list\')');
        $update->execute();
    }
}
?>
