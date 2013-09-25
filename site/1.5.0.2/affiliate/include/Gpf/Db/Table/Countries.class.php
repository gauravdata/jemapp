<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
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
class Gpf_Db_Table_Countries extends Gpf_DbEngine_Table {
    const ID = 'countryid';
    const COUNTRY_CODE = 'countrycode';
    const COUNTRY = 'country';
    const STATUS = 'status';
    const ORDER = "rorder";
    const ACCOUNTID = 'accountid';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_countries');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::COUNTRY_CODE, 'char', 8);
        $this->createColumn(self::COUNTRY, 'char', 80);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::ORDER, 'int');
        $this->createColumn(self::ACCOUNTID, 'char', 8);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::COUNTRY_CODE, self::ACCOUNTID), $this->_('Country code must by unique peer account')));
    }
}
?>
