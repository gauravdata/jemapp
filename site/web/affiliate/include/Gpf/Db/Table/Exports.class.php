<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Exports.class.php 19734 2008-08-08 07:36:13Z mbebjak $
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
class Gpf_Db_Table_Exports extends Gpf_DbEngine_Table {
    const ID = 'exportid';
    const FILENAME = 'filename';
    const DATETIME = 'datetime';
    const DESCRIPTION = 'description';
    const DATA_TYPES = 'datatypes';
    const ACCOUNT_ID = 'accountid';
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_exports');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::FILENAME, 'char', 255);
        $this->createColumn(self::DATETIME, 'datetime');
        $this->createColumn(self::DESCRIPTION, 'char', 255);
        $this->createColumn(self::DATA_TYPES, 'char', 255);
        $this->createColumn(self::ACCOUNT_ID, 'char', 8);
    }
}

?>
