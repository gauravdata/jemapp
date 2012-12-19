<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: Channel.class.php 18660 2008-06-19 15:30:59Z aharsani $
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
class Pap_Db_Channel extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_Channels::getInstance());
        parent::init();
    }
    
    public function getId() {
    	return $this->get(Pap_Db_Table_Channels::ID);
    }
    
    public function setId($value) {
    	$this->set(Pap_Db_Table_Channels::ID, $value);
    }
        
    public function getValue() {
    	$value = $this->get(Pap_Db_Table_Channels::VALUE);
    	if($value != null && $value != '') {
    		return $value;
    	}
    	
    	return $this->get(Pap_Db_Table_Channels::ID);
    }
    
    public function setValue($value) {
    	return $this->set(Pap_Db_Table_Channels::VALUE, $value);
    }
        
    public function getName() {
    	return $this->get(Pap_Db_Table_Channels::NAME);
    }

    public function setName($value) {
    	$this->set(Pap_Db_Table_Channels::NAME, $value);
    }
    
    public function setPapUserId($value) {
    	$this->set(Pap_Db_Table_Channels::USER_ID, $value);
    }

    /**
     * @return Pap_Db_Channel
     * @throws Gpf_Exception
     */
    public static function loadFromId($channelId, $userId) {
        $channel = new Pap_Db_Channel();
        $channel->setPrimaryKeyValue($channelId);
        $channel->setPapUserId($userId);
        try {
            $channel->loadFromData(array(Pap_Db_Table_Channels::ID, Pap_Db_Table_Channels::USER_ID));
            return $channel;
        } catch (Gpf_DbEngine_NoRowException $e) {
            $channel->setValue($channelId);
            $channel->loadFromData(array(Pap_Db_Table_Channels::VALUE, Pap_Db_Table_Channels::USER_ID));
            return $channel;
        }
    }
}
?>
