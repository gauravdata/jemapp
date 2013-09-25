<?php
class pap_update_4_5_60 {

    public function execute() {
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE  `qu_pap_visits1` DROP INDEX  `fk_qu_pap_visits1_qu_g_accounts1`');
        } catch (Exception $e) {
        }
    }
}
?>
