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
class Pap_Db_Table_VisitorAffiliates extends Gpf_DbEngine_Table {
    const ID = 'visitoraffiliateid';
    const VISITORID = 'visitorid';
    const USERID = 'userid';
    const BANNERID = 'bannerid';
    const CAMPAIGNID = 'campaignid';
    const CHANNELID = 'channelid';
    const TYPE = 'rtype';
    const IP = 'ip';
    const DATEVISIT = 'datevisit';
    const VALIDTO = 'validto';
    const REFERRERURL = 'referrerurl';
    const DATA1 = 'data1';
    const DATA2 = 'data2';
    const ACCOUNTID = 'accountid';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_visitoraffiliates');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::INT, 0, true);
        $this->createColumn(self::VISITORID, self::CHAR, 36);
        $this->createColumn(self::USERID, self::CHAR, 8);
        $this->createColumn(self::BANNERID, self::CHAR, 8);
        $this->createColumn(self::CAMPAIGNID, self::CHAR, 8);
        $this->createColumn(self::CHANNELID, self::CHAR, 8);
        $this->createColumn(self::TYPE, self::CHAR, 1);
        $this->createColumn(self::IP, self::CHAR, 39);
        $this->createColumn(self::DATEVISIT, self::DATETIME);
        $this->createColumn(self::VALIDTO, self::DATETIME);
        $this->createColumn(self::REFERRERURL, self::CHAR);
        $this->createColumn(self::DATA1, self::CHAR, 255);
        $this->createColumn(self::DATA2, self::CHAR, 255);
        $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
    }

}
?>
