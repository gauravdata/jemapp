<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FilterConditions.class.php 19030 2008-07-08 19:28:16Z mfric $
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
class Gpf_Db_Table_FilterConditions extends Gpf_DbEngine_Table {
	const FIELD_ID = "fieldid";
	const FILTER_ID = "filterid";
	const SECTION_CODE = "sectioncode";
	const CODE = "code";
	const OPERATOR = "operator";
	const VALUE = "value";
		
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_filter_conditions');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::FIELD_ID, 'char', 50, true);
        $this->createColumn(self::FILTER_ID, 'char', 8);
        $this->createColumn(self::SECTION_CODE, 'char', 50);
        $this->createColumn(self::CODE, 'char', 50);
        $this->createColumn(self::OPERATOR, 'char', 3);
        $this->createColumn(self::VALUE, 'char', 250);
    }

    public function deleteAll($filterId) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add('filterid', '=', $filterId);
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}

?>
