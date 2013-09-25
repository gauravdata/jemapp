<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: File.class.php 22128 2008-11-05 10:46:57Z vzeman $
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
class Gpf_Db_File extends Gpf_DbEngine_Row {
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Files::getInstance());
        parent::init();
    }

    public function insert() {
        $this->set('created', Gpf_DbEngine_Database::getDateString());
        $this->set('downloads', 0);
        $this->set('referenced', 0);
        return parent::insert();
    }

    public function incrementDownloads() {
        $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
        $updateBuilder->from->add(Gpf_Db_Table_Files::getName());
        $updateBuilder->set->add('downloads', 'downloads + 1', false);
        $updateBuilder->where->add('fileid', '=', $this->get('fileid'));
        return $updateBuilder->update();
    }

    private function updateReferenced($increment = 1) {
        $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
        $updateBuilder->from->add(Gpf_Db_Table_Files::getName());
        $updateBuilder->set->add('referenced', 'referenced + (' + $increment + ')', false);
        $updateBuilder->where->add('fileid', '=', $this->get('fileid'));
        if ($increment < 0) {
            $updateBuilder->where->add('referenced', '>', 0);
        }
        return $updateBuilder->update();
    }
    
    /**
     * @return Gpf_File_DownloadDriver
     */
    public function createDriver() {
        $path = $this->getPath();
        if(!strlen($path)) {
            return new Gpf_File_Download_Db($this);
        }
        
        $fileName = rtrim($this->getPath(), '/\\') . '/' . $this->getFileName();
        if(substr($fileName, 0, 1) == '.') {
            $fileName = Gpf_Paths::getInstance()->getTopPath() . $fileName;
        }
        return new Gpf_File_Download_FileSystem($fileName);
    }
    
    
    /**
     * Return download url to file
     *
     * @param string $serverClass Server class name, which will handle download function 
     * @return string
     */
    public function getUrl($serverClass = 'Gpf_File_Download') {
        return Gpf_Paths::getInstance()->getBaseServerUrl() . 'scripts/server.php' . 
        '?C=' . $serverClass . '&M=download&S=' . Gpf_Session::getInstance()->getId() . 
        '&FormRequest=Y&FormResponse=Y&fileid=' . $this->getFileId() 
        . '&attachment=Y';
    }
    
    /**
     * Deletes row. Primary key value must be set before this function is called
     */
    public function delete() {
        $this->load();
        if ($this->getReferenced() > 0) {
            return;
        }
        parent::delete();
    }
    
    public function addReference() {
        return $this->updateReferenced(1);
    }

    public function removeReference() {
        return $this->updateReferenced(-1);
    }
    
    public function getFileName() {
        return $this->get(Gpf_Db_Table_Files::FILE_NAME);
    }

    public function getType() {
        return $this->get(Gpf_Db_Table_Files::FILE_TYPE);
    }
    
    public function getPath() {
        return $this->get(Gpf_Db_Table_Files::PATH);
    }
    
    public function getSize() {
        return $this->get(Gpf_Db_Table_Files::FILE_SIZE);
    }
    
    public function setFileName($name) {
        $this->set(Gpf_Db_Table_Files::FILE_NAME, $name);
    }
    
    public function setFileSize($size) {
        $this->set(Gpf_Db_Table_Files::FILE_SIZE, $size);
    }
    
    public function setPath($path) {
        $this->set(Gpf_Db_Table_Files::PATH, $path);
    }
    
    public function getFileId() {
        return $this->get(Gpf_Db_Table_Files::ID);
    }
    
    public function getReferenced() {
        return $this->get(Gpf_Db_Table_Files::REFERENCED);
    }
    
    public function setFileId($fileId) {
        $this->set(Gpf_Db_Table_Files::ID, $fileId);
    }
    
    public function setAccountUserId($accountUserId) {
        $this->set('accountuserid', $accountUserId);
    }
}
