<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Window.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Wallpaper extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Wallpapers::getInstance());
        parent::init();
    }
    
    public function setFileId($fileId) {
        $this->set(Gpf_Db_Table_Wallpapers::FILEID, $fileId);
    }
    
    public function getFileId() {
        return $this->get(Gpf_Db_Table_Wallpapers::FILEID);
    }
    
    public function setAccountUserId($accountUserId) {
        $this->set(Gpf_Db_Table_Wallpapers::ACCOUNTUSERID, $accountUserId);
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_Wallpapers::NAME, $name);
    }
    
    public function setUrl($url) {
        $this->set(Gpf_Db_Table_Wallpapers::URL, $url);
    }
    
    public function getUrl() {
        return $this->get(Gpf_Db_Table_Wallpapers::URL);
    }
    
   /**
     * Deletes row. Primary key value must be set before this function is called
     */
    public function delete() {
        if($this->isPrimaryKeyEmpty()) {
            throw new Gpf_Exception("Could not delete Row. Primary key values are empty");
        }
        $this->load();
        if ($this->getFileId() != "") {
            try {
                $file = new Gpf_Db_File();
                $file->setFileId($this->getFileId());
                $file->removeReference();
            } catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
            }
        }
        
        return parent::delete();
    }
}

?>
