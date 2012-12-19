<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Accounts.class.php 21565 2008-10-14 06:41:43Z mjancovic $
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
class Gpf_Db_Table_InstalledTemplates extends Gpf_DbEngine_Table {
    const ID = 'templateid';
    const NAME = 'name';
    const VERSION = 'version';
    const CHANGED = 'changed';
    const HASH = 'contenthash';
    const OVERWRITE_EXISTING = 'overwritte_existing';
    
    /**
     * @var Gpf_Db_Table_InstalledTemlates
     */
    private static $instance;
    
    /**
     *
     * @return Gpf_Db_Table_InstalledTemlates
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_installedtemplates');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 32);
        $this->createColumn(self::NAME, 'text');
        $this->createColumn(self::VERSION, 'char', 40);
        $this->createColumn(self::HASH, 'char', 32);
        $this->createColumn(self::CHANGED, 'date');
        $this->createColumn(self::OVERWRITE_EXISTING, 'char', 1);
    }
}

?>
