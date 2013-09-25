<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GadgetProperties.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_GadgetProperties extends Gpf_DbEngine_Table {

    const ID = 'gadgetpropertyid';
    const GADGETID = 'gadgetid';
    const NAME = 'name';
    const VALUE = 'value';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_gadgetproperties');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::GADGETID, 'char', 8);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::VALUE, 'char');
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::NAME, self::GADGETID)));
    }
    
    public static function deleteAll($gadgetId) {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(self::getName());
        $delete->where->add(self::GADGETID, '=', $gadgetId);
        $delete->execute();
    }
}

?>
