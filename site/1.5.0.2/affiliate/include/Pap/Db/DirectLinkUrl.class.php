<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_DirectLinkUrl extends Gpf_DbEngine_Row {
	
	const DEFAULT_STATUS = "P";
        
    function __construct(){
        parent::__construct();
    }
    
    function init() {
        $this->setTable(Pap_Db_Table_DirectLinkUrls::getInstance());
        parent::init();
    }
    
    public function getUrl() {
        return $this->get(Pap_Db_Table_DirectLinkUrls::URL);
    }
    
    public function getNote() {
        return $this->get(Pap_Db_Table_DirectLinkUrls::NOTE);
    }

    public function getPapUserId() {
        return $this->get(Pap_Db_Table_DirectLinkUrls::USER_ID);
    }
    
    public function setId($value) {
    	$this->set(Pap_Db_Table_DirectLinkUrls::ID, $value);
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_DirectLinkUrls::ID);
    }
    
    public function setCampaignId($value) {
        $this->set(Pap_Db_Table_DirectLinkUrls::CAMPAIGN_ID, $value);
    }
    
    public function getCampaignId() {
        return $this->get(Pap_Db_Table_DirectLinkUrls::CAMPAIGN_ID);
    }
   
    public function setPapUserId($value) {
    	$this->set(Pap_Db_Table_DirectLinkUrls::USER_ID, $value);
    }
    
    public function setUrl($value) {
    	$this->set(Pap_Db_Table_DirectLinkUrls::URL, $value);
    }
    
    public function setStatus($value) {
    	$this->set(Pap_Db_Table_DirectLinkUrls::STATUS, $value);
    }
     	
    public function checkUserApprovedDirectLinks($userId) {
        $result = new Gpf_Data_RecordSet();
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('COUNT('.Pap_Db_Table_DirectLinkUrls::ID.')', 'count');
        $selectBuilder->from->add(Pap_Db_Table_DirectLinkUrls::getName());
        $selectBuilder->where->add(Pap_Db_Table_DirectLinkUrls::USER_ID, '=', $userId);
        $selectBuilder->where->add(Pap_Db_Table_DirectLinkUrls::STATUS, '=', Pap_Common_Constants::STATUS_APPROVED);

        $result->load($selectBuilder);

        if($result->getSize() == 0) {
            return false;
        }

        foreach($result as $record) {
            if($record->get('count') > 0) {
                return true;
            }
            break;
        }

        return false;
    }
    
    public function set($name, $value) {
        parent::set($name, trim($value));
    }
}

?>
