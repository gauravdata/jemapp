<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Currencies.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Currencies extends Gpf_DbEngine_Table {
    const ID = 'currencyid';
    const NAME = 'name';
    const SYMBOL = 'symbol';
    const PRECISION = 'cprecision';
    const IS_DEFAULT = "isdefault";
    const WHEREDISPLAY = 'wheredisplay';
    const EXCHANGERATE = 'exchrate';
    const ACCOUNTID = 'accountid';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_currencies');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::SYMBOL, 'char', 20);
        $this->createColumn(self::PRECISION, 'tinyint');
        $this->createColumn(self::IS_DEFAULT, 'tinyint');
        $this->createColumn(self::WHEREDISPLAY, 'tinyint');
        $this->createColumn(self::EXCHANGERATE, 'float', 1);
        $this->createColumn(self::ACCOUNTID, 'char', 8);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::NAME, self::ACCOUNTID)));
    }
}
?>
