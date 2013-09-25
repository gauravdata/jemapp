<?php
class pap_update_4_3_7 {
    public function execute() {
        $this->deleteUnnecessaryEntries('qu_pap_monthlyclicks', 'qu_pap_dailyclicks');
        $this->deleteUnnecessaryEntries('qu_pap_monthlyimpressions', 'qu_pap_dailyimpressions');
    }
    
    private function deleteUnnecessaryEntries($monthlyTable, $dailyTable) {
        try {
            $oldestEntry = new Gpf_DateTime($this->getOldestEntry($dailyTable));
            $this->deleteNewEntries($monthlyTable, $oldestEntry);
        } catch (Gpf_Exception $e) {
        }
    }
    
    private function deleteNewEntries($tableName, Gpf_DateTime $fromDate) {
        $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
        for ($i = $fromDate->getDay(); $i<=31; $i++) {
            $updateBuilder->set->add("raw_$i", 0);
            $updateBuilder->set->add("unique_$i", 0);
            if (strstr($tableName, 'click')) {
                $updateBuilder->set->add("declined_$i", 0);
            }
        }
        $updateBuilder->from->add($tableName);
        $updateBuilder->where->add('month', '=', $fromDate->getMonthStart()->toDate());
        $updateBuilder->execute();
        
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add($tableName);
        $delete->where->add('month', '>', $fromDate->toDate());
        $delete->execute();
    }
    
    private function getOldestEntry($table) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('day');
        $selectBuilder->from->add($table);
        $selectBuilder->orderBy->add('day');
        $selectBuilder->limit->set(0, 1);
        $row = $selectBuilder->getOneRow();
        return $row->get('day');
    }
}
?>
