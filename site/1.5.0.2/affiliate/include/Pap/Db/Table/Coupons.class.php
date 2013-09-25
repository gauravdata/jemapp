<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Campaigns.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Table_Coupons extends Gpf_DbEngine_Table {
    const ID = 'couponid';
    const CODE = 'couponcode';
    const STATUS = 'rstatus';
    const USERID = 'userid';
    const BANNERID = 'bannerid';
    const VALID_FROM = 'validfrom';
    const VALID_TO = 'validto';
    const MAX_USE_COUNT = 'maxusecount';

    private static $instance;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initName() {
        $this->setName('pap_coupons');
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::CODE, self::CHAR, 100);
        $this->createColumn(self::STATUS, self::CHAR, 1);
        $this->createColumn(self::USERID, self::CHAR, 8);
        $this->createColumn(self::BANNERID, self::CHAR, 8);
        $this->createColumn(self::VALID_FROM, self::DATETIME);
        $this->createColumn(self::VALID_TO, self::DATETIME);
        $this->createColumn(self::MAX_USE_COUNT, self::INT);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::CODE),
                                    $this->_("Coupon code must be unique")));
    }
}
?>
