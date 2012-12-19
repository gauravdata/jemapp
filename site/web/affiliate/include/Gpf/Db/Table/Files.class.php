<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Files.class.php 20871 2008-09-12 13:07:51Z mbebjak $
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
class Gpf_Db_Table_Files extends Gpf_DbEngine_Table {
    const ID = 'fileid';
    const ACCOUNTUSERID = 'accountuserid';
    const CREATED = 'created';
    const FILE_NAME = 'filename';
    const FILE_SIZE = 'filesize';
    const FILE_TYPE = 'filetype';
    const PATH = 'path';
    const DOWNLOADS = 'downloads';
    const REFERENCED = 'referenced';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_files');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 32, true);
        $this->createColumn(self::ACCOUNTUSERID, 'char', 8);
        $this->createColumn(self::CREATED, 'datetime');
        $this->createColumn(self::FILE_NAME, 'char', 255);
        $this->createColumn(self::FILE_SIZE, 'int');
        $this->createColumn(self::FILE_TYPE, 'char', 255);
        $this->createColumn(self::PATH, 'text');
        $this->createColumn(self::DOWNLOADS, 'int');
        $this->createColumn(self::REFERENCED, 'int');
    }

    protected function initConstraints() {
       $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_FileContents::ID, new Gpf_Db_FileContent());
    }     

    public function deleteAll($fileId) {
        $content = Gpf_Db_Table_FileContents::getInstance();
        $content->deleteAll($fileId);
        
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add('fileid', '=', $fileId);
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}

?>
