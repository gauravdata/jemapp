<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Gpf_Data_Grid extends Gpf_Object {
    /**
     * @var Gpf_Data_RecordSet
     */
	private $recordset;
    private $totalCount;
    
    public function loadFromObject(stdClass  $object) {
        $this->recordset = new Gpf_Data_RecordSet();
        $this->recordset->loadFromObject($object->rows);
        $this->totalCount = $object->count;
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getRecordset() {
    	return $this->recordset;
    }
    
    public function getTotalCount() {
    	return $this->totalCount;
    }
}

?>
