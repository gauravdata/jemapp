<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package GwtPhpFramework
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Export.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Export extends Gpf_DbEngine_Row {
        
    function __construct(){
        parent::__construct();
    }
    
    function init() {
        $this->setTable(Gpf_Db_Table_Exports::getInstance());
        parent::init();
    }

    public function setFileName($fileName) {
    	$this->set(Gpf_Db_Table_Exports::FILENAME, $fileName);
    }
    
    public function getFileName() {
        return $this->get(Gpf_Db_Table_Exports::FILENAME);
    }

    public function setDateTime($dateTime) {
        $this->set(Gpf_Db_Table_Exports::DATETIME, $dateTime);
    }
    
    public function setDescription($description) {
        $this->set(Gpf_Db_Table_Exports::DESCRIPTION, $description);
    }
    
    public function setDataTypes($dataTypes) {
        $this->set(Gpf_Db_Table_Exports::DATA_TYPES, $dataTypes);
    }
    
    public function setAccountId($accountId) {
    	$this->set(Gpf_Db_Table_Exports::ACCOUNT_ID, $accountId);
    }
}

?>
