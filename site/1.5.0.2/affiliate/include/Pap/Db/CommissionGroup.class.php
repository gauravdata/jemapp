<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Andrej Harsani
*   @since Version 1.0.0
*   $Id: CommissionGroup.class.php 29493 2010-10-07 12:16:48Z iivanco $
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
class Pap_Db_CommissionGroup extends Gpf_DbEngine_Row {
	
	const COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN = -1;

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_CommissionGroups::getInstance());
        parent::init();
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_CommissionGroups::ID);
    }
    public function setId($value) {
        $this->set(Pap_Db_Table_CommissionGroups::ID, $value);
    }
        
    public function getIsDefault() {
    	return $this->get(Pap_Db_Table_CommissionGroups::IS_DEFAULT);
    }
    
    /**
     * @return int cookie lifetime in seconds OR -1 if is not defined
     */
    public function getCookieLifetime() {
    	if ($this->get(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME) > self::COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN) {
    		return Pap_Tracking_Cookie::computeLifeTimeDaysToSeconds($this->get(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME));
    	}
    	return self::COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN;
    }
    
    public function setCookieLifetime($value) {
       $this->set(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME, $value);
    }
    
    public function getCampaignId() {
        return $this->get(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
    }
        
    public function setDefault($value) {
        $this->set(Pap_Db_Table_CommissionGroups::IS_DEFAULT, $value);
    }
    
    public function getDefault() {
        return $this->get(Pap_Db_Table_CommissionGroups::IS_DEFAULT);
    }
    
    public function setCampaignId($campaignId) {
        $this->set(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, $campaignId);
    }
    
    public function setName($name) {
        $this->set(Pap_Db_Table_CommissionGroups::NAME, $name);
    }
    
    public function getName() {
        return $this->get(Pap_Db_Table_CommissionGroups::NAME);
    }
    
     public function getPriority() {
        return $this->get(Pap_Db_Table_CommissionGroups::PRIORITY);
    }
    
    public function setPriority($priority) {
        $this->set(Pap_Db_Table_CommissionGroups::PRIORITY, $priority);
    }
    
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $commiossionGroupId
     * @return Pap_Db_CommissionGroup
     */
    public static function getCommissionGroupById($commiossionGroupId) {
        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setPrimaryKeyValue($commiossionGroupId);
        $commissionGroup->load();
        return $commissionGroup;
    }
}

?>
