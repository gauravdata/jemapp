<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Views.class.php 19377 2008-07-24 13:28:48Z mbebjak $
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
class Gpf_Db_Table_Wallpapers extends Gpf_DbEngine_Table {

    const ID = 'wallpaperid';
    const ACCOUNTUSERID = 'accountuserid';
    const FILEID = 'fileid';
    const NAME = 'name';
    const URL = 'url';

    private static $instance;
        
    /**
     * @return Gpf_Db_Table_Wallpapers
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_wallpapers');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::ACCOUNTUSERID, self::CHAR, 8);
        $this->createColumn(self::FILEID, self::CHAR, 32);
        $this->createColumn(self::NAME, self::CHAR, 255);
        $this->createColumn(self::URL, self::CHAR, 255);
    }
    
    protected function initConstraints() {
       $this->addCascadeDeleteConstraint(self::FILEID, Gpf_Db_Table_Files::ID, new Gpf_Db_File());
    }
     
    
    /**
     * @param String $viewtype
     *
     * @return Gpf_Data_RecordSet
     */
    public function getAllWallpapers() {
        $result = new Gpf_Data_RecordSet();

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'id');
        $selectBuilder->select->add(self::NAME, 'name');
        $selectBuilder->select->add(self::URL, 'url');
        $selectBuilder->select->add(self::FILEID, 'fileid');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::ACCOUNTUSERID, '=', Gpf_Session::getAuthUser()->getAccountUserId());

        $result->load($selectBuilder);
        return $result;
    }
}

?>
