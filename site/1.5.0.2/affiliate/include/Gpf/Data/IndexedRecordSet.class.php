<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: IndexedRecordSet.class.php 22730 2008-12-09 16:07:54Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Data_IndexedRecordSet extends Gpf_Data_RecordSet {
    private $key;

    /**
     *
     * @param int $keyIndex specifies which column should be used as a key
     */
    function __construct($key) {
        parent::__construct();
        $this->key = $key;
    }
    
    public function addRecord(Gpf_Data_Record $record) {
        $this->_array[$record->get($this->key)] = $record;
    }
    
    /**
     * @param String $keyValue
     * @return Gpf_Data_Record
     */
    public function createRecord($keyValue = null) {
        if($keyValue === null) {
            return parent::createRecord();
        }
        if(!array_key_exists($keyValue, $this->_array)) {
            $record = $this->createRecord();
            $record->set($this->key, $keyValue);
            $this->addRecord($record);
        }
        return $this->_array[$keyValue];
    }
    
    protected function loadRecordFromObject(Gpf_Data_Record $record) {    
        $this->_array[$record->get($this->key)] = $record; 
    }                
        
    /**
     * @param String $keyValue
     * @return Gpf_Data_Record
     */
    public function getRecord($keyValue = null) {
        if (!isset($this->_array[$keyValue])) {
            throw new Gpf_Data_RecordSetNoRowException($keyValue);
        }
        return $this->_array[$keyValue];
    }
    
    /**
     * @param String $keyValue
     * @return boolean
     */
    public function existsRecord($keyValue) {
        return isset($this->_array[$keyValue]);
    }
    
    /**
     * @param String $sortOptions (SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING)
     * @return boolean
     */
    public function sortByKeyValue($sortOptions) {
        return array_multisort($this->_array, $sortOptions);
    }
}

?>
