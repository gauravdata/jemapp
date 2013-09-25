<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GridService.class.php 23899 2009-03-24 08:35:42Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_View_MemoryGridService extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function loadResultData() {
        $this->_count = 0;
        return parent::loadResultData();
    }
    
    public function filterRow(Gpf_Data_Row $row) {
        if ($this->getLimit() === null || ($this->getOffSet() <= $this->_count && $this->getLimit() + $this->getOffSet() > $this->_count)) {
            $this->_count++;
            return $row;
        } 
        $this->_count++;
        return null;
    }
    
    protected function computeCount() {        
    }
    
    protected function buildLimit() {
        $this->initLimit();
    }
}
?>
