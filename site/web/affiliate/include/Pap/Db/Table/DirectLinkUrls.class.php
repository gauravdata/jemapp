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
class Pap_Db_Table_DirectLinkUrls extends Gpf_DbEngine_Table {
    const ID = 'directlinkurlid';
    const USER_ID = 'userid';
    const URL = 'url';
    const STATUS = 'rstatus';
    const NOTE = 'note';
    const CHANNEL_ID = 'channelid';
    const CAMPAIGN_ID = 'campaignid';
    const BANNER_ID = 'bannerid';
    const MATCHES = 'matches';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_directlinkurls');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::USER_ID, 'char', 8);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::URL, 'char', 255);
        $this->createColumn(self::NOTE, 'char', 100000);
        $this->createColumn(self::CHANNEL_ID, 'char', 8);
        $this->createColumn(self::CAMPAIGN_ID, 'char', 8);
        $this->createColumn(self::BANNER_ID, 'char', 8);
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
            array(self::URL),
            $this->_("URL must be unique.")));
    }
}
?>
