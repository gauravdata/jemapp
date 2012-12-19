<?php
class pap_update_4_5_29 {
    public function execute() {
        $filter = new Gpf_Db_Filter();
        $filter->setFilterId('afftreed');
        $filter->setName('default');
        $filter->setFilterType('subaffiliatetree');
        $filter->setNull(Gpf_Db_Table_Filters::USER_ID);
        $filter->setPreset('Y');
        try {
            $filter->save();
        } catch (Gpf_DbEngine_DuplicateEntryException $e) {}
        
        $condition = new Gpf_Db_FilterCondition();
        $condition->setFieldId('rstatus');
        $condition->setFilterId('afftreed');
        $condition->setSectionCode('default');
        $condition->setCode('rstatus');
        $condition->setOperator('IN');
        $condition->setValue('A');
        try {
            $condition->save();
        } catch (Gpf_DbEngine_DuplicateEntryException $e) {}
    }
}

?>
