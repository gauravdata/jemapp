<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Galik
*   @since Version 1.0.0
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
*   Version 1.0 (the "License"); you may not use this file except in compliance
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
*
*/

/**
 * @package PostAffiliatePro
 * @deprecated
 */
class Pap_Db_Table_Visitors extends Gpf_DbEngine_Table {
    const ID = 'visitorid';
    const NAME = 'name';
    const EMAIL = 'email';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_visitors');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 36);
        $this->createColumn(self::NAME, self::CHAR, 100);
        $this->createColumn(self::EMAIL, self::CHAR, 60);
    }
}
?>
