<?php
class pap_update_4_5_69 {
    public function execute() {
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE  `qu_pap_transactions` ADD INDEX  `IDX_qu_pap_transactions_data1` (  `data1` )');
        } catch (Exception $e) {
        }
    }
}
?>
