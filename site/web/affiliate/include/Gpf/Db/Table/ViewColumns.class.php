<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ViewColumns.class.php 22411 2008-11-20 22:17:14Z aharsani $
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
class Gpf_Db_Table_ViewColumns extends Gpf_DbEngine_Table {
    
    const NAME = 'name';
    const VIEW_ID = 'viewid';
    const SORTED = 'sorted';
    const WIDTH = 'width';
    const ORDER = 'rorder';
    
    private static $instance;
        
    /**
     * @return Gpf_Db_Table_ViewColumns
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_view_columns');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::NAME, 'char', 50);
        $this->createPrimaryColumn(self::VIEW_ID, 'char', 8);
        $this->createColumn(self::SORTED, 'char', 1);
        $this->createColumn(self::WIDTH, 'char', 6);
        $this->createColumn(self::ORDER, 'int', 0);
    }

    /**
     * @return Gpf_Data_IndexedRecordSet
     */
    public function fillViewColumns(Gpf_Data_IndexedRecordSet $allColumns, $viewid) {
        $viewColumns = new Gpf_Data_RecordSet();

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('name', 'name');
        $selectBuilder->select->add('sorted', 'sorted');
        $selectBuilder->select->add('width', 'width');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add('viewid', '=', $viewid);
        $selectBuilder->orderBy->add('rorder');
        $viewColumns->load($selectBuilder);
         
        foreach ($viewColumns as $viewColumn) {
            try {
                $column = $allColumns->getRecord($viewColumn->get('name'));
                $column->set('sorted', $viewColumn->get('sorted'));
                $column->set('width', $viewColumn->get('width'));
                $column->set('visible', Gpf::YES);
            } catch (Exception $e) {
            }
        }
        return $allColumns;
    }

    public function deleteAll($viewId) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add('viewid', '=', $viewId);
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}

?>
