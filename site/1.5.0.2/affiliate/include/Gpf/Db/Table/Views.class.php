<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Views.class.php 29307 2010-09-14 09:32:12Z iivanco $
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
class Gpf_Db_Table_Views extends Gpf_DbEngine_Table {

    const ID = 'viewid';
    const VIEWTYPE = 'viewtype';
    const NAME = 'name';
    const ROWSPERPAGE = 'rowsperpage';
    const ACCOUNTUSERID = 'accountuserid';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_views');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::VIEWTYPE, self::CHAR, 100);
        $this->createColumn(self::NAME, self::CHAR, 100);
        $this->createColumn(self::ROWSPERPAGE, self::INT, 0);
        $this->createColumn(self::ACCOUNTUSERID, self::CHAR, 8);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
            array(self::VIEWTYPE, self::NAME, self::ACCOUNTUSERID),
            $this->_("View name must be unique")));
    }

    /**
     * @param String $viewtype
     *
     * @return Gpf_Data_RecordSet
     */
    public function getAllViews($viewtype) {
        $result = new Gpf_Data_RecordSet('id');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'id');
        $selectBuilder->select->add(self::NAME, self::NAME);
        $selectBuilder->select->add(self::ROWSPERPAGE, self::ROWSPERPAGE);
        $selectBuilder->select->add(self::ACCOUNTUSERID, self::ACCOUNTUSERID);
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::VIEWTYPE, '=', $viewtype);
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(self::ACCOUNTUSERID, '=', Gpf_Session::getAuthUser()->getAccountUserId(), 'OR');
        $condition->add(self::ACCOUNTUSERID, '=', '', 'OR');
        $selectBuilder->where->addCondition($condition);
        $selectBuilder->orderBy->add(self::ACCOUNTUSERID, false);
        
        $result->load($selectBuilder);
        return $result;
    }
}

?>
