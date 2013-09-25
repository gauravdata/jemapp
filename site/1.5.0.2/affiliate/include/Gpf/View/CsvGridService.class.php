<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak, Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: CampaignsGrid.class.php 23615 2009-02-26 08:38:00Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
abstract class Gpf_View_CsvGridService extends Gpf_View_MemoryGridService {

    /**
     * @var Gpf_Io_Csv_Reader
     */
    private $csvReader;

    protected function buildFrom() {
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function loadResultData() {
        $result = $this->initResult();

        $this->_count = 0;
        foreach ($this->csvReader as $row) {
            $row = $this->filterRow($row);
            if ($row !== null) {
                $result->add($this->fillResultRecord($result->createRecord(), $row));
            }
        }
        return $result;
    }
    
    private function fillResultRecord(Gpf_Data_Record $record, Gpf_Data_Record $row) {
        foreach ($this->dataColumns as $columnId => $column) {
            try {
                $record->set($columnId, $row->get($column->getName()));
            } catch (Gpf_Exception $e) {
                $record->set($columnId, '');
            }
        }
        return $record;
    }
    
    protected function buildFilter() {
    }

    public function filterRow(Gpf_Data_Row $row) {
        if (!$this->filters->matches($row)) {
            return null;
        }
        return parent::filterRow($row);
    }

    protected function setCsvReader(Gpf_Io_Csv_Reader $reader) {
        $this->csvReader = $reader;
    }
}
?>
