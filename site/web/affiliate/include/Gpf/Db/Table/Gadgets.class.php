<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadgets.class.php 20871 2008-09-12 13:07:51Z mbebjak $
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
class Gpf_Db_Table_Gadgets extends Gpf_DbEngine_Table {

    const ID = 'gadgetid';
    const ACCOUNTUSERID = 'accountuserid';
    const TYPE = 'rtype';
    const NAME = 'name';
    const URL = 'url';
    const POSITION_TYPE = 'positiontype';
    const POSITION_TOP = 'positiontop';
    const POSITION_LEFT = 'positionleft';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const AUTOREFRESH_TIME = "autorefreshtime";
    
    
    const POSITION_TYPE_DESKTOP = 'D';
    const POSITION_TYPE_SIDEBAR = 'S';
    const POSITION_TYPE_HIDDEN = 'H';
    
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_gadgets');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::ACCOUNTUSERID, 'char', 8);
        $this->createColumn(self::TYPE, 'char', 1);
        $this->createColumn(self::NAME, 'char', 80);
        $this->createColumn(self::URL, 'char', 250);
        $this->createColumn(self::POSITION_TYPE, 'char', 1);
        $this->createColumn(self::POSITION_TOP, 'int');
        $this->createColumn(self::POSITION_LEFT, 'int');
        $this->createColumn(self::WIDTH, 'int');
        $this->createColumn(self::HEIGHT, 'int');
        $this->createColumn(self::AUTOREFRESH_TIME, 'int');
    }
    
    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_GadgetProperties::GADGETID, new Gpf_Db_GadgetProperty());
    }

    /**
     *
     * @param string $accountUserId
     * @return Gpf_Data_RecordSet
     */
    public static function getGadgets($accountUserId, $gadgetId = null) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID);
        $selectBuilder->select->add(self::NAME);
        $selectBuilder->select->add(self::URL);
        $selectBuilder->select->add(self::POSITION_TYPE);
        $selectBuilder->select->add(self::POSITION_TOP);
        $selectBuilder->select->add(self::POSITION_LEFT);
        $selectBuilder->select->add(self::WIDTH);
        $selectBuilder->select->add(self::HEIGHT);
        $selectBuilder->select->add(self::AUTOREFRESH_TIME);
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::ACCOUNTUSERID, '=', $accountUserId);
        $selectBuilder->where->add(self::POSITION_TYPE, '<>', Gpf_Gadget::POSITION_HIDDEN);
        if ($gadgetId != null) {
            $selectBuilder->where->add(self::ID, '=', $gadgetId);
        }
        $selectBuilder->orderBy->add(self::POSITION_TOP);
        return $selectBuilder->getAllRows();        
    }
}

?>
