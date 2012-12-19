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
class Pap_Db_Table_BannerWrappers extends Gpf_DbEngine_Table {
    const ID = 'wrapperid';
    const NAME = 'name';
    const CODE = 'code';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_bannerwrappers');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::NAME, self::CHAR, 80);
        $this->createColumn(self::CODE, self::CHAR);
    }

    protected function initConstraints() {
        $this->addRestrictDeleteConstraint(self::ID, Pap_Db_Table_Banners::WRAPPER_ID,new Pap_Db_Banner());
    }
}
?>
