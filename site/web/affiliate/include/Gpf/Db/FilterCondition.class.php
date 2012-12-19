<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FilterCondition.class.php 19030 2008-07-08 19:28:16Z mfric $
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
class Gpf_Db_FilterCondition extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_FilterConditions::getInstance());
        parent::init();
    }
    
    public function setFieldId($value) {
        $this->set(Gpf_Db_Table_FilterConditions::FIELD_ID, $value);
    }
    
   public function setFilterId($value) {
    	return $this->set(Gpf_Db_Table_FilterConditions::FILTER_ID, $value);
    }
        
    public function setSectionCode($value) {
    	return $this->set(Gpf_Db_Table_FilterConditions::SECTION_CODE, $value);
    }

    public function setCode($value) {
    	return $this->set(Gpf_Db_Table_FilterConditions::CODE, $value);
    }

    public function setOperator($value) {
    	return $this->set(Gpf_Db_Table_FilterConditions::OPERATOR, $value);
    }

    public function setValue($value) {
    	return $this->set(Gpf_Db_Table_FilterConditions::VALUE, $value);
    }    

}
