<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package Gpf
 *   @since Version 1.0.0
 *   $Id: DataSet.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package Gpf
 */
interface Gpf_Search_Model_DataSet {
    
    /**
     *
     * @return Gpf_Data_RecordSet
     */
    public function getData();
    
    public function getAllCount();
    
    public function setPage($pageNr);
    
    public function setSort($code);
    
    public function getSortOptions();
    
    public function getRecordsPerPage();

    public function getRecordsName();
}
?>
