<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 29662 2010-10-25 08:32:20Z mbebjak $
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
class Gpf_Db_Table_Logs extends Gpf_DbEngine_Table {
    const ID = "logid";
    const GROUP_ID = "groupid";
    const TYPE = "rtype";
    const CREATED = "created";
    const FILENAME = "filename";
    const LEVEL = "level";
    const LINE = "line";
    const MESSAGE = "message";
    const ACCOUNT_USER_ID = "accountuserid";
    const IP = "ip";
	
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_logs');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::INT, 0, true);
        $this->createColumn(self::GROUP_ID, self::CHAR, 16);
        $this->createColumn(self::TYPE, self::CHAR, 1);
        $this->createColumn(self::CREATED, self::DATETIME);
        $this->createColumn(self::FILENAME, self::CHAR, 255);
        $this->createColumn(self::LEVEL, self::INT);
        $this->createColumn(self::LINE, self::INT);
        $this->createColumn(self::MESSAGE, self::CHAR);
        $this->createColumn(self::ACCOUNT_USER_ID, self::CHAR, 8);
        $this->createColumn(self::IP, self::CHAR, 39);
    }

    public function deleteAll($logId) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add(self::ID, '=', $logId);
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}
?>
