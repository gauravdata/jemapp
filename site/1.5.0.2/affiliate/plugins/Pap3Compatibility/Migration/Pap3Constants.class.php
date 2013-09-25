<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
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

class Pap3Compatibility_Migration_Pap3Constants extends Gpf_Object {
   
	const DEFAULT_ACCOUNT_ID = 'default1';
	
    const TRANSTYPE_CLICK = 1;
    const TRANSTYPE_LEAD = 2;
    const TRANSTYPE_SALE = 4;
    const TRANSTYPE_RECURRING = 8;
    const TRANSTYPE_SIGNUP = 16;
    const TRANSTYPE_CPM = 32;
    const TRANSTYPE_REFERRAL = 64;
    const TRANSTYPE_REFUND = 128;
    const TRANSTYPE_CHARGEBACK = 256;

    const TRANSKIND_NORMAL = 1;
    const TRANSKIND_RECURRING = 3;
    const TRANSKIND_SECONDTIER = 10;

    const RECURRINGTYPE_MONTHLY = 1;
    const RECURRINGTYPE_WEEKLY = 2;
    const RECURRINGTYPE_QUARTERLY = 3;
    const RECURRINGTYPE_BIANNUALLY = 4;
    const RECURRINGTYPE_YEARLY = 5;
    
	const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_DECLINED = 3;

    const BANNERTYPE_TEXT = 1;
    const BANNERTYPE_IMAGE = 2;
    const BANNERTYPE_HTML = 3;
    const BANNERTYPE_POPUP = 4;
    const BANNERTYPE_POPUNDER = 5;
    const BANNERTYPE_ROTATOR = 6;
    const BANNERTYPE_TEXTEMAIL = 14;
    const BANNERTYPE_HTMLEMAIL = 15;
    
    static public function translateStatus($pap3Status) {
    	switch($pap3Status) {
    		case self::STATUS_PENDING: return Pap_Common_Constants::STATUS_PENDING; 
    		case self::STATUS_APPROVED: return Pap_Common_Constants::STATUS_APPROVED; 
    		case self::STATUS_DECLINED: return Pap_Common_Constants::STATUS_DECLINED;
    		default:  return Pap_Common_Constants::STATUS_PENDING;
    	}
    }

    static public function translateTransType($pap3Type) {
    	switch($pap3Type) {
    		case self::TRANSTYPE_SALE: return Pap_Common_Constants::TYPE_SALE; 
    		case self::TRANSTYPE_LEAD: return Pap_Common_Constants::TYPE_ACTION; 
    		case self::TRANSTYPE_CLICK: return Pap_Common_Constants::TYPE_CLICK; 
    		case self::TRANSTYPE_CPM: return Pap_Common_Constants::TYPE_CPM; 
    		
    		case self::TRANSTYPE_RECURRING: return Pap_Common_Constants::TYPE_RECURRING; 
    		case self::TRANSTYPE_SIGNUP: return Pap_Common_Constants::TYPE_SIGNUP; 
    		case self::TRANSTYPE_REFERRAL: return Pap_Common_Constants::TYPE_REFERRAL; 
    		case self::TRANSTYPE_REFUND: return Pap_Common_Constants::TYPE_REFUND; 
    		case self::TRANSTYPE_CHARGEBACK: return Pap_Common_Constants::TYPE_CHARGEBACK; 
    
    		default:  return Pap_Common_Constants::TYPE_SALE;
    	}
    }
    
    static public function translateBannerType($pap3Type) {
    	switch($pap3Type) {
    		case self::BANNERTYPE_TEXT: return Pap_Common_Banner_Factory::BannerTypeText; 
    		case self::BANNERTYPE_IMAGE: return Pap_Common_Banner_Factory::BannerTypeImage; 
    		case self::BANNERTYPE_HTML: return Pap_Common_Banner_Factory::BannerTypeHtml; 
    		case self::BANNERTYPE_POPUP: return Pap_Common_Banner_Factory::BannerTypePopup; 
    		case self::BANNERTYPE_POPUNDER: return Pap_Common_Banner_Factory::BannerTypePopunder; 
    		case self::BANNERTYPE_ROTATOR: return 'R';
    		case self::BANNERTYPE_TEXTEMAIL: return Pap_Common_Banner_Factory::BannerTypePromoEmail;
    		case self::BANNERTYPE_HTMLEMAIL: return Pap_Common_Banner_Factory::BannerTypePromoEmail;
    		default:  return Pap_Common_Banner_Factory::BannerTypeHtml;
    	}
    }

    static public function translateIpValidity($pap3Type) {
    	switch($pap3Type) {
    		case 'minutes': return Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES; 
    		case 'hours': return Pap_Merchants_Config_TrackingForm::VALIDITY_HOURS; 
    		case 'days': return Pap_Merchants_Config_TrackingForm::VALIDITY_DAYS; 
    		default:  return Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES;
    	}
    }    
}
?>
