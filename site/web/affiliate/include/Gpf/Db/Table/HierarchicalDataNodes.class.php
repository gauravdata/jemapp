<?php
/**
 *   @copyright Copyright (c) 2010 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActiveViews.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Gpf_Db_Table_HierarchicalDataNodes extends Gpf_DbEngine_Table {

    const ID = "nodeid";
    const CODE = "code";
    const TYPE = "type";
    const NAME = "name";
    const LFT = "lft";
    const RGT = "rgt";
    const STATE = "state";
    const DATE_INSERTED = "dateinserted";
    
    private static $instance;
        
    /**
     * @return Pap_Db_Table_HierarchicalDataNodes
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_hierarchicaldatanodes');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::INT, 0, true);
        $this->createColumn(self::TYPE, self::CHAR, 8);
        $this->createColumn(self::CODE, self::INT);
        $this->createColumn(self::NAME, self::CHAR, 200);
        $this->createColumn(self::LFT, self::INT);
        $this->createColumn(self::RGT, self::INT);
        $this->createColumn(self::STATE, self::CHAR, 1);
        $this->createColumn(self::DATE_INSERTED, self::DATETIME);
    }
}

?>
