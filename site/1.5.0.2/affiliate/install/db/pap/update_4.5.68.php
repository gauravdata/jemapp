<?php
class pap_update_4_5_68 {
    public function execute() {
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE  `qu_pap_transactions` ADD INDEX  `IDX_qu_pap_transactions_orderid` (  `orderid` )');
        } catch (Exception $e) {
        }
    }
}
?>
