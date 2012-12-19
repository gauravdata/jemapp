<?php
class pap_update_4_5_20 {
    public function execute() {
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('SELECT `merchantnote` FROM `qu_pap_accountings`');
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE `qu_pap_accountings` DROP `merchantnote`');
        } catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
        }
        
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('SELECT `systemnote` FROM `qu_pap_accountings`');
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE `qu_pap_accountings` DROP `systemnote`');
        } catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
        }
        
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('SELECT `invoiceid` FROM `qu_pap_accountings`');    
        } catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE `qu_pap_accountings` ADD `invoiceid` CHAR( 8 ) DEFAULT NULL AFTER `accountid`');
        }
        
    }
}
?>
