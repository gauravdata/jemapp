<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
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
class Pap_Db_Table_CpmCommissions extends Gpf_DbEngine_Table {
    const USERID = "userid";
    const BANNERID = "bannerid";
    const COUNT = "count";
    
    private static $instance;
        
    /**
     * @return Pap_Db_Table_CpmCommissions
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_cpmcommissions');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::USERID, self::CHAR, 8);
        $this->createPrimaryColumn(self::BANNERID, self::CHAR, 8);
        $this->createColumn(self::COUNT, self::INT);
    }

    protected function initConstraints() {
    }
}

?>
