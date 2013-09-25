<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Versions extends Gpf_DbEngine_Table {
    const ID = 'versionid';
    const NAME = 'name';
    const APPLICATION = 'application';
    const DONE_DATE = 'done';
    
    /**
     *
     * @var Gpf_Db_Table_Versions
     */
    private static $instance;
        
    /**
     *
     * @return Gpf_Db_Table_Versions
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_versions');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    public function getLatestVersion($application) {
        if(!is_array($application)) {
            $application = array($application);
        }
        
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->add('name');
        $sql->from->add(self::getName(), 'v');
        $sql->where->add('application', 'IN', $application);
        $sql->where->add('done', '!=', null);
        $sql->orderBy->add('done', false);
        $sql->orderBy->add('versionid', false);
        $sql->limit->set(0, 1);;
        $rows = $sql->getAllRows();
        if($rows->getSize() == 0) {
            return false;
        }
        return $rows->getRecord(0)->get('name');
    }
    
    public function isExists() {
        try {
            $this->getLatestVersion('');
            return true;
        } catch (Exception $e) {
        }
        return false;
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::APPLICATION, 'char', 40);
        $this->createColumn(self::DONE_DATE, 'datetime');
    }
}

?>
