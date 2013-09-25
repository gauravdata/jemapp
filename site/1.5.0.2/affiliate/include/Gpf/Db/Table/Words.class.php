<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Words.class.php 29662 2010-10-25 08:32:20Z mbebjak $
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
class Gpf_Db_Table_Words extends Gpf_DbEngine_Table {
    const ID = 'wordid';
    const WORD_TEXT = 'wordtext';
    const WORD_LENGTH = 'wordlength';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_words');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::WORD_TEXT, 'varchar', 255);
        $this->createColumn(self::WORD_LENGTH, 'int');
        for ($i=1; $i<=16; $i++) {
            $this->createColumn("w$i", 'char', 2);
        }
    }

    public function deleteAll($id) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add(self::ID, '=', $id);
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}

?>
