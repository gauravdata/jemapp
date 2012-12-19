<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package GwtPhpFramework
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: ImportExport.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_ImportExport extends Gpf_DbEngine_Row {
        
    function __construct(){
        parent::__construct();      
    }
    
    function init() {
        $this->setTable(Gpf_Db_Table_ImportExports::getInstance());
        parent::init();
    }
        
    public function setName($name) {
    	$this->set(Gpf_Db_Table_ImportExports::NAME, $name);
    }

    public function setDescription($description) {
        $this->set(Gpf_Db_Table_ImportExports::DESCRIPTION, $description);
    }
    
    public function setClassName($className) {
        $this->set(Gpf_Db_Table_ImportExports::CLASS_NAME, $className);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_ImportExports::ACCOUNT_ID, $accountId);
    }
    
    public function getClassName() {
    	return $this->get(Gpf_Db_Table_ImportExports::CLASS_NAME);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_ImportExports::NAME);
    }
    
    public function setCode($code) {
        $this->set(Gpf_Db_Table_ImportExports::CODE, $code);
    }
    
    public function getCode() {
        return $this->get(Gpf_Db_Table_ImportExports::CODE);
    }
    
    /**
     * @param String $code
     * @return String $className
     */
    public static function getClassNameFromCode($code) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_ImportExports::CLASS_NAME);
        $select->from->add(Gpf_Db_Table_ImportExports::getName());
        $select->where->add(Gpf_Db_Table_ImportExports::CODE, "=", $code);
        $className = $select->getOneRow();
        
        return $className->get(Gpf_Db_Table_ImportExports::CLASS_NAME);
    }
    
    /**
     * @param String $code
     * @return String $name
     */
    public static function getNameFromCode($code) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_ImportExports::NAME);
        $select->from->add(Gpf_Db_Table_ImportExports::getName());
        $select->where->add(Gpf_Db_Table_ImportExports::CODE, "=", $code);
        $className = $select->getOneRow();
        
        return $className->get(Gpf_Db_Table_ImportExports::NAME);
    }
    
    /**
     * Get all importExport rows with columns (CLASS_NAME, CODE)
     *
     * @return Gpf_Data_RecordSet
     */
    public static function getImportExportObjects() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_ImportExports::CLASS_NAME);
        $select->select->add(Gpf_Db_Table_ImportExports::CODE);
        $select->select->add(Gpf_Db_Table_ImportExports::NAME);
        $select->from->add(Gpf_Db_Table_ImportExports::getName());
        $importExportObjects = $select->getAllRows();
        
        return $importExportObjects;
    }
}

?>
