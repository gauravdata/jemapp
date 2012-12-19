<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * Class for constants that are used by multiple objects or tables
 *
 * @package PostAffiliatePro
 */
class Pap_Common_Constants  {
	/**
	 * status constants
	 * for transaction, affiliate
	 *
	 */
    const STATUS_APPROVED = 'A';
    const STATUS_PENDING = 'P';
    const STATUS_DECLINED = 'D';

	/**
	 * enable status constants
	 *
	 */
    const ESTATUS_ENABLED = 'E';
    const ESTATUS_DISABLED = 'D';
    
   	/**
	 * payout status constants
	 *
	 */
    const PSTATUS_PAID = 'P';
    const PSTATUS_UNPAID = 'U';
        
    /**
     * returns text name for this status
     */
    public static function getStatusAsText($status) {
    	switch($status) {
    		case self::STATUS_APPROVED: return Gpf_Lang::_('approved');
    		case self::STATUS_PENDING: return Gpf_Lang::_('pending');
    		case self::STATUS_DECLINED: return Gpf_Lang::_('declined');
    	}
    	return Gpf_Lang::_('unknown');
    }

    /**
     * returns text name for this payout status
     */
    public static function getPayoutStatusAsText($payoutStatus) {
    	switch($payoutStatus) {
    		case self::PSTATUS_PAID: return Gpf_Lang::_('paid');
    		case self::PSTATUS_UNPAID: return Gpf_Lang::_('unpaid');
    	}
    	return Gpf_Lang::_('unknown');
    }
    
    /**
     * type constants for transactions types
     */
    const TYPE_CPM = 'I';
    const TYPE_CLICK = 'C';
    const TYPE_SALE = 'S';
    const TYPE_LEAD = 'L';
    const TYPE_ACTION = 'A';
    const TYPE_SIGNUP = 'B';
    const TYPE_RECURRING = 'U';
    const TYPE_REFERRAL = 'F';
    const TYPE_REFUND = 'R';
    const TYPE_CHARGEBACK = 'H';
    const TYPE_EXTRABONUS = 'E';
    
    /**
     * returns text name for this type
     */
    public static function getTypeAsText($type) {
    	switch($type) {
    		case Pap_Common_Constants::TYPE_ACTION: return Gpf_Lang::_("action");
    		case Pap_Common_Constants::TYPE_CLICK: return Gpf_Lang::_("click");
    		case Pap_Common_Constants::TYPE_CPM: return Gpf_Lang::_("cpm");
    		case Pap_Common_Constants::TYPE_SALE: return Gpf_Lang::_("sale");
    		case Pap_Common_Constants::TYPE_LEAD: return Gpf_Lang::_("lead");
    		case Pap_Common_Constants::TYPE_SIGNUP: return Gpf_Lang::_("signup");
    		case Pap_Common_Constants::TYPE_RECURRING: return Gpf_Lang::_("recurring");
    		case Pap_Common_Constants::TYPE_REFERRAL: return Gpf_Lang::_("referral");
    		case Pap_Common_Constants::TYPE_REFUND: return Gpf_Lang::_("refund");
    		case Pap_Common_Constants::TYPE_CHARGEBACK: return Gpf_Lang::_("chargeback");
    		case Pap_Common_Constants::TYPE_EXTRABONUS: return Gpf_Lang::_("extra bonus");
    	}
    	
    	return Gpf_Lang::_("unknown");
    }
    
    /**
     * returns text name for campaign type
     */
    public static function getCampaignTypeAsText($type) {
    	switch($type) {
    		case Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION: return Gpf_Lang::_("private");
    		case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL: return Gpf_Lang::_("public with approval");
    		case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC: return Gpf_Lang::_("public");
    	}
    	
    	return $obj->_("unknown");
    }
    
    
    /**
     * fieldgroup constants
     *
     */
    const FIELDGROUP_TYPE_PAYOUTOPTION = 'P';
    const FIELDGROUP_TYPE_SIGNUPACTION = 'A';
    const FIELDGROUP_TYPE_AFTERSALEACTION = 'S';
    
    
    const SMARTY_SYNTAX_URL = 'troubleshooting/invalid-smarty-syntax.html';
}
?>
