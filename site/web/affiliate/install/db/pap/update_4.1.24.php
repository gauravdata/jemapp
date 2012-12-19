<?php
/**
 *
 */
class pap_update_4_1_24 {
    public function execute() {
        $q = new Gpf_SqlBuilder_SelectBuilder();
        $q->select->add('fixedcost');
        $q->from->add(Pap_Db_Table_Transactions::getName());
        $q->limit->set(0,1);
        try {
            $q->getAllRows();
            return;
        } catch(Exception $e) {
        }
        
        $db = Gpf_DbEngine_Database::getDatabase();
        $db->execute("ALTER TABLE qu_pap_transactions ADD fixedcost FLOAT NOT NULL DEFAULT '0'");
    }
}
?>
