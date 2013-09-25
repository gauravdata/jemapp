<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ImportExports.class.php 19734 2008-08-08 07:36:13Z mbebjak $
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
class Gpf_Db_Table_ImportExports extends Gpf_DbEngine_Table {
    const ID = 'importexportid';
    const NAME = 'name';
    const CODE = 'code';
    const DESCRIPTION = 'description';
    const CLASS_NAME = 'classname';
    const ACCOUNT_ID = 'accountid';
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_importexport');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'char', 40);
        $this->createColumn(self::CODE, 'char', 40);
        $this->createColumn(self::DESCRIPTION, 'char', 255);
        $this->createColumn(self::CLASS_NAME, 'char', 255);
        $this->createColumn(self::ACCOUNT_ID, 'char', 8);
    }
}

?>
