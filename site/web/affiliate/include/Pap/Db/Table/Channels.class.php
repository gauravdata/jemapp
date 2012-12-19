<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Channels.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Db_Table_Channels extends Gpf_DbEngine_Table {
	
    const ID = 'channelid';
    const USER_ID = 'userid';
    const NAME = 'name';
    const VALUE = 'channel';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_channels');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::USER_ID, 'char', 8);
        $this->createColumn(self::NAME, 'char', 255);
        $this->createColumn(self::VALUE, 'char', 10);
    }
    
    protected function initConstraints() {
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::CHANNEL, new Pap_Db_RawClick());
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Clicks::CHANNEL, new Pap_Db_Click());
       
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Impressions::CHANNEL, new Pap_Db_Impression());
       
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::CHANNEL_ID, new Pap_Db_DirectLinkUrl());
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Transactions::CHANNEL, new Pap_Db_Transaction());
    }
    
    public static function getUserChannels($userId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(self::ID);
        $select->select->add(self::NAME);
        $select->select->add(self::VALUE);
        $select->from->add(self::getName());
        $select->where->add(self::USER_ID, "=", $userId);
    
        return $select->getAllRows();
    }
}
?>
