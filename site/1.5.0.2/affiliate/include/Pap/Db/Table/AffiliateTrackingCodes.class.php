<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Pap_Db_Table_AffiliateTrackingCodes extends Gpf_DbEngine_Table {

    const ID = "affiliatetrackingcodeid";
    const AFFILIATEID = "userid";
    const COMMTYPEID = "commtypeid";
    const CODE = "code";
    const NOTE = "note";
    const R_STATUS = "rstatus";
    const TYPE = "rtype";
    private static $instance;
        
    /**
     * @return Pap_Db_Table_AffiliateTrackingCode
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_affiliatetrackingcodes');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::AFFILIATEID, self::CHAR, 8);
        $this->createColumn(self::COMMTYPEID, self::CHAR, 8);
        $this->createColumn(self::CODE, self::CHAR);
        $this->createColumn(self::NOTE, self::CHAR);
        $this->createColumn(self::R_STATUS, self::CHAR, 1);
        $this->createColumn(self::TYPE, self::CHAR, 1);
    }

    protected function initConstraints() {
         $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
            array(self::AFFILIATEID, self::COMMTYPEID)));
    }

}

?>
