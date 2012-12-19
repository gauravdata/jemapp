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
 */
class Pap_Db_Table_RawImpressions extends Gpf_DbEngine_Table {
    const ID = 'impressionid';
    const DATE = 'date';
    const RTYPE = 'rtype';
    const USERID = 'userid';
    const BANNERID = 'bannerid';
    const PARENTBANNERID = 'parentbannerid';
    const CHANNEL = 'channel';
    const IP = 'ip';
    const DATA1 = 'data1';
    const DATA2 = 'data2';

    private static $instance;
    
    private $index;

    public static function getInstance($index) {
        if(@self::$instance[$index] === null) {
            self::$instance[$index] = new self;
            self::$instance[$index]->index = $index;
        }
        return self::$instance[$index];
    }
    
    public function name() {
        return parent::name() . $this->index;
    }

    protected function initName() {
        $this->setName('pap_impressions');
    }

    public static function getName($index) {
        return self::getInstance($index)->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int');
        $this->createColumn(self::DATE, 'datetime');
        $this->createColumn(self::RTYPE, 'char', 1);
        $this->createColumn(self::USERID, 'char', 8);
        $this->createColumn(self::BANNERID, 'char', 8);
        $this->createColumn(self::PARENTBANNERID, 'char', 8);
        $this->createColumn(self::CHANNEL, 'char', 10);
        $this->createColumn(self::IP, 'char', 39);
        $this->createColumn(self::DATA1, 'char', 255);
        $this->createColumn(self::DATA2, 'char', 255);
    }

}
?>
