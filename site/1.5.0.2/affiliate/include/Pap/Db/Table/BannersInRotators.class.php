<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banners.class.php 21342 2008-09-30 14:27:11Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Db_Table_BannersInRotators extends Gpf_DbEngine_Table {
    const ID = 'bannerinrotatorid';
    const PARENT_BANNER_ID = 'parentbannerid';
    const ROTATED_BANNER_ID = 'rotatedbannerid';
    const ALL_IMPS = 'all_imps';
    const UNIQ_IMPS = 'uniq_imps';
    const CLICKS = 'clicks';
    const RANK = 'rank';
    const VALID_FROM = 'valid_from';
    const VALID_UNTIL = 'valid_until';
    const ARCHIVE = 'archive';
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('pap_bannersinrotators');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::PARENT_BANNER_ID, self::CHAR, 8);
        $this->createColumn(self::ROTATED_BANNER_ID, self::CHAR, 8);
        $this->createColumn(self::ALL_IMPS, self::INT, 11);
        $this->createColumn(self::UNIQ_IMPS, self::INT, 11);
        $this->createColumn(self::CLICKS, self::INT, 11);
        $this->createColumn(self::RANK, self::INT);
        $this->createColumn(self::VALID_FROM, self::DATETIME);
        $this->createColumn(self::VALID_UNTIL, self::DATETIME);
        $this->createColumn(self::ARCHIVE, self::CHAR, 1);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
                                    array(self::PARENT_BANNER_ID => Pap_Db_Table_Banners::ID), 
                                    new Pap_Db_Banner()));
        $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
                                    array(self::ROTATED_BANNER_ID => Pap_Db_Table_Banners::ID), 
                                    new Pap_Db_Banner()));
    }
}
?>
