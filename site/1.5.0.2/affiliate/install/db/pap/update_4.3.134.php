<?php

class pap_update_4_3_134 {
    public function execute() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Transactions::getName());
        $dateTime = Gpf_DateTime::min();
        $update->set->add(Pap_Db_Table_Transactions::DATA2, $dateTime->toDateTime());
        $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $compoundCondition->add(Pap_Db_Table_Transactions::DATA2,'=' ,'AT');
        $compoundCondition->add(Pap_Db_Table_Transactions::DATA2,'=' , 'AUC', 'OR');
        $update->where->addCondition($compoundCondition);
        $update->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_EXTRA_BONUS);
    }
}
?>
