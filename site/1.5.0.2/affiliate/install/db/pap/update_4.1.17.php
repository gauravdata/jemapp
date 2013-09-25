<?php

class pap_update_4_1_17 {
    public function execute() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_CommissionTypes::ID);
        $selectBuilder->from->add(Pap_Db_Table_CommissionTypes::getName());
        $selectBuilder->where->add(Pap_Db_Table_CommissionTypes::TYPE, '=', Pap_Common_Constants::TYPE_REFERRAL);
        try {
            $selectBuilder->getOneRow();
            return;
        } catch (Gpf_Exception $e) {
        }

        $insert = new Gpf_SqlBuilder_InsertBuilder();
        $insert->setTable(Pap_Db_Table_CommissionTypes::getInstance());
        $insert->add(Pap_Db_Table_CommissionTypes::ID, 'refercom');
        $insert->add(Pap_Db_Table_CommissionTypes::TYPE, Pap_Common_Constants::TYPE_REFERRAL);
        $insert->add(Pap_Db_Table_CommissionTypes::STATUS, Pap_Db_CommissionType::STATUS_ENABLED);
        $insert->add(Pap_Db_Table_CommissionTypes::APPROVAL, Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
        $insert->add(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, Gpf::NO);
        try {
            $insert->execute();
        } catch (Exception $e) {
        }
    }
}
?>
