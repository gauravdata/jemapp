<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Windows.class.php 20866 2008-09-12 11:39:19Z mbebjak $
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
class Gpf_Db_Table_Windows extends Gpf_DbEngine_Table {

    const ACCOUNTUSERID = 'accountuserid';
    const CONTENT = 'content';
    const POSITION_TOP = 'positiontop';
    const POSITION_LEFT = 'positionleft';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const CLOSED = 'closed';
    const MINIMIZED = 'minimized';
    const ZINDEX = 'zindex';
    const AUTOREFRESHTIME = 'autorefreshtime';
    
    private static $instance;
        
    /**
     * @return Gpf_Db_Table_Windows
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_windows');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ACCOUNTUSERID, 'char');
        $this->createPrimaryColumn(self::CONTENT, 'text');
        $this->createColumn(self::POSITION_TOP, 'int');
        $this->createColumn(self::POSITION_LEFT, 'int');
        $this->createColumn(self::WIDTH, 'int');
        $this->createColumn(self::HEIGHT, 'int');
        $this->createColumn(self::CLOSED, 'char');
        $this->createColumn(self::MINIMIZED, 'char');
        $this->createColumn(self::ZINDEX, 'int');
        $this->createColumn(self::AUTOREFRESHTIME, 'int');
    }

    /**
     *
     * @param string $accountUserId
     * @return Gpf_Data_RecordSet
     */
    public function getWindows($accountUserId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('content');
        $selectBuilder->select->add('positiontop');
        $selectBuilder->select->add('positionleft');
        $selectBuilder->select->add('width');
        $selectBuilder->select->add('height');
        $selectBuilder->select->add('closed');
        $selectBuilder->select->add('minimized');
        $selectBuilder->select->add('autorefreshtime');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add('accountuserid', '=', $accountUserId);
        $selectBuilder->orderBy->add('zindex');
        /**** Load only top window ****/
        //$selectBuilder->orderBy->add('zindex', false);
        //$selectBuilder->limit->set(0, 1);
        return $selectBuilder->getAllRows();        
    }
    
    public static function setAllWindowClosed($accountUserId) {
    	$updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
    	$updateBuilder->set->add('closed', 'Y');
        $updateBuilder->from->add(self::getName());
        $updateBuilder->where->add('accountuserid', '=', $accountUserId);
        $updateBuilder->execute();
    }
}

?>
