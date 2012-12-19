<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_RecurrenceSettings extends Gpf_DbEngine_Table {
    const ID = 'recurrencesettingid';
    const RECURRENCEPRESETID = 'recurrencepresetid';
    const TYPE = 'type';
    const PERIOD = 'period';
    const FREQUENCY = 'frequency';
    
    /**
     *
     * @var Gpf_Db_Table_Tasks
     */
    private static $instance;
        
    /**
     *
     * @return Gpf_Db_Table_Tasks
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_recurrencesettings');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::RECURRENCEPRESETID, self::CHAR, 8);
        $this->createColumn(self::TYPE, self::CHAR, 1);
        $this->createColumn(self::PERIOD, self::INT);
        $this->createColumn(self::FREQUENCY, self::INT);
    }
}

?>
