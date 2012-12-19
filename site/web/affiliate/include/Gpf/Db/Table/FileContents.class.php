<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FileContents.class.php 20860 2008-09-12 08:54:43Z mbebjak $
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
class Gpf_Db_Table_FileContents extends Gpf_DbEngine_Table {

    const ID = 'fileid';
    const CONTENTID = 'contentid';
    const CONTENT = 'content';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_filecontents');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 32, false);
        $this->createPrimaryColumn(self::CONTENTID, 'int', 0, false);
        $this->createColumn(self::CONTENT, 'text');
    }

    public function deleteAll($fileId) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add('fileid', '=', $fileId);
        $this->createDatabase()->execute($deleteBulider->toString());
    }

    /**
     * Return file content from database
     *
     * @param string $fileId
     * @return string
     */
    public static function getFileContent($fileId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        
        $select->select->add('content', 'content');
        $select->from->add(Gpf_Db_Table_FileContents::getName());
        $select->where->add('fileid', '=', $fileId);
        $select->orderBy->add('contentid');
          
        $resultSet = $select->getAllRows();
        $content = '';
        foreach ($resultSet as $result) {
            $content .= $result->get('content');
        }
        return $content;
    }
}
?>
