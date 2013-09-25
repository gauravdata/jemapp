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

class Pap3Compatibility_Migration_TaskSettings extends Gpf_Object {
	
	private $pap3Settings = array();
	
    public function run() {
    	echo "Migrating settings<br/>";
    	$time1 = microtime();
    	
    	try {
    		$this->loadPap3Settings();
    		$this->migrateDefaultCampaign();
    		$this->migrateGeneralSettings();
    	} catch(Exception $e) {
    		echo "&nbsp;&nbsp;Errror: ".$e->getMessage()."<br/>";
    	}
    	
    	$time2 = microtime();
    	Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }
    
    private function loadPap3Settings() {
    	echo "&nbsp;&nbsp;Loading PAP3 settings.....";
    	
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('code', 'code');
        $selectBuilder->select->add('value', 'value');
        $selectBuilder->from->add('wd_g_settings');
        $selectBuilder->where->add('userid', '=', null);
        $selectBuilder->where->add('id1', '=', null);
        $selectBuilder->where->add('id2', '=', null);          
        $selectBuilder->where->add('code', 'LIKE', 'Aff_%');

        $count = 0;
        foreach($selectBuilder->getAllRowsIterator() as $record) {
			$this->pap3Settings[$record->get('code')] = $record->get('value');
        	$count++;
        }

    	echo " ($count) ..... DONE<br/>";
    }
    
    private function getPap3Setting($code) {
    	if(!isset($this->pap3Settings[$code])) {
    		return '';
    	}
    	return $this->pap3Settings[$code];
    }
    
    private function migrateDefaultCampaign() {
    	echo "&nbsp;&nbsp;Migrating default campaign.....";
    	
    	$defaultCampaignId = $this->getPap3Setting('Aff_default_campaign');
    	if($defaultCampaignId == '' || !$this->isExistsCampaign($defaultCampaignId)) {
    		$defaultCampaignId = $this->chooseSomeCampaignAsDefault();
    	}
		if($defaultCampaignId != '') {
    		$campaign = new Pap_Db_Campaign();
    		$campaign->setId($defaultCampaignId);
    		$campaign->load();
    		$campaign->setIsDefault();
    		$campaign->save();
		}

    	echo " ..... DONE<br/>";
    }
    
    private function isExistsCampaign($campaignId) {
        $campaign = new Pap_Db_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return false;
        }
        return true;
    }

    private function chooseSomeCampaignAsDefault() {
        	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('*');
        
        $selectBuilder->from->add('qu_pap_campaigns', 'c');

        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	if($record->get('rtype') == Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC 
        		&& $record->get('rstatus') == Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE) 
        	{
        		return $record->get('campaignid');
       		}
        }

        // no active and public campaign found
        foreach($selectBuilder->getAllRowsIterator() as $record) {
        	if($record->get('rstatus') == Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE) {
        		return $record->get('campaignid');
       		}
        }
        
        // no active campaign found
		return '';        
    }
    
    private function migrateGeneralSettings() {
    	Gpf_Settings::set(Pap_Settings::MAIN_SITE_URL, $this->getPap3Setting('Aff_main_site_url'));	
    	
    	$this->migrateCurrency();
    	$this->migrateEmailNotifications();
		$this->migrateMailAccount();
		$this->migrateFraudProtection();
		$this->migrateAffiliateSettings();
		$this->migrateTrackingAndCookies();
		$this->migratePayoutsSettings();
		//affiliate signup
    }
    
    private function convert1ToY($value) {
    	if($value == 1) {
    		return 'Y';
    	} else if($value == 0 || $value == '') {
    		return 'N';
    	}
    	
    	return $value;
    }

    private function migrateCurrency() {
    	echo "&nbsp;&nbsp;Migrating currency.....";
    	$currency = $this->getPap3Setting('Aff_system_currency');
    	$currencyPosition = $this->getPap3Setting('Aff_currency_left_position');
    	$currencyRounding = $this->getPap3Setting('Aff_round_numbers');
    	
    	$obj = new Gpf_Db_Currency();
    	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
    	$obj->setExchangeRate(1);
    	$obj->setIsDefault(1);
    	$obj->setName($currency);
    	$obj->setSymbol($currency);
    	$obj->setPrecision($currencyRounding);
    	$obj->setWhereDisplay(($currencyPosition == 1 ? Gpf_Db_Currency::DISPLAY_LEFT : Gpf_Db_Currency::DISPLAY_RIGHT));
    	
    	$obj->save();
    	echo "DONE<br/>";
    }
    
    private function migrateEmailNotifications() {
    	echo "&nbsp;&nbsp;Migrating email notifications.....";
    	
    	Gpf_Settings::set(Pap_Settings::NOTIFICATION_ON_SALE, 
    						$this->convert1ToY($this->getPap3Setting('Aff_email_onsale')));	
    	Gpf_Settings::set(Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME, 
    						$this->convert1ToY($this->getPap3Setting('Aff_email_onaffsignup')));
    	echo "DONE<br/>";
    }    
    
    private function migrateMailAccount() {
    	echo "&nbsp;&nbsp;Migrating mail account.....";
    	
    	$systemEmail = $this->getPap3Setting('Aff_system_email');
    	$systemEmailName = $this->getPap3Setting('Aff_system_email_name');
    	$mailSendType = $this->getPap3Setting('Aff_mail_send_type'); //1 - mail, 2 - smtp
    	
    	$obj = new Gpf_Db_MailAccount();
    	$obj->setAccountId(Pap3Compatibility_Migration_Pap3Constants::DEFAULT_ACCOUNT_ID);
    	if($systemEmail != '') {
    		$obj->setAccountEmail($systemEmail);
    	} else {
    		$obj->setAccountEmail('some@email.com');
    	}
    	$obj->setAccountName(($systemEmailName != '' ? $systemEmailName : $systemEmail));
    	$obj->setAsDefault(true);
    	if($systemEmailName != '') {
    		$obj->setFromName($systemEmailName);
    	}
    	if($mailSendType == 1) {
    		$obj->setUseSmtp(false);
    	} else {
    		$obj->setUseSmtp(true);
    		$obj->setSmtpServer($this->getPap3Setting('Aff_smtp_server'));
    		$obj->setSmtpUser($this->getPap3Setting('Aff_smtp_username'));
    		$obj->setSmtpPassword($this->getPap3Setting('Aff_smtp_password'));
    		$smtpPort = $this->getPap3Setting('Aff_smtp_server_port');
    		if($smtpPort == '') {
    			$smtpPort = 25;
    		}
    		$obj->setSmtpPort($smtpPort);
    		if($this->getPap3Setting('Aff_smtp_server_tls') == 1) {
    			$obj->setSmtpUseAthentication(true);
    		}
    	}

    	$obj->save();
    	
	   	echo "DONE<br/>";
    }
    
    private function migrateFraudProtection() {
    	echo "&nbsp;&nbsp;Migrating fraud protection.....";
    	
    	if($this->getPap3Setting('Aff_declinefrequentclicks') == 1) {
    		 Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_SETTING_NAME, Gpf::YES);
    		 Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME, 
    		 					$this->getPap3Setting('Aff_clickfrequency'));
    		 Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME, 
    		 	($this->getPap3Setting('Aff_frequentclicks') == 2 ? 'DS' : 'D'));
    	}

    	if($this->getPap3Setting('Aff_declinefrequentsales') == 1) {
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME, Gpf::YES);
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME, 
    		 					$this->getPap3Setting('Aff_salefrequency'));
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME, 
    		 	($this->getPap3Setting('Aff_frequentsales') == 2 ? 'DS' : 'D'));
    	}
    	
    	if($this->getPap3Setting('Aff_declinesameorderid') == 1) {
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME, Gpf::YES);
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME, 
    		 					$this->getPap3Setting('Aff_saleorderidfrequency'));
    		 Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME, 
    		 	($this->getPap3Setting('Aff_frequentclicks') == 2 ? 'DS' : 'D'));
    		 Gpf_Settings::set(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME, Gpf::NO);
    	}
    	
    	echo "DONE<br/>";
    }

    private function migrateAffiliateSettings() {
    	echo "&nbsp;&nbsp;Migrating affiliate settings.....";
    	
    	Gpf_Settings::set(Pap_Settings::AFFILIATE_APPROVAL,
    						($this->getPap3Setting('Aff_affiliateapproval') == 1 ? 'M' : 'A'));
    	
    	Gpf_Settings::set(Pap_Settings::AFFILIATE_LOGOUT_URL, 
    						$this->getPap3Setting('Aff_afflogouturl'));

		Gpf_Settings::set(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME, 
							Pap_Merchants_Config_AffiliateSignupForm::POST_SIGNUP_TYPE_URL);
							    						
    	Gpf_Settings::set(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME, 
    						$this->getPap3Setting('Aff_affpostsignupurl'));
    						
    	echo "DONE<br/>";
    }
    
    private function migratePayoutsSettings() {
        echo "&nbsp;&nbsp;Migrating payout settings.....";
        
        if ($this->getPap3Setting('Aff_min_payout_options') != '') {
            Gpf_Settings::set(Pap_Settings::PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME,
                            str_replace(';', ',', $this->getPap3Setting('Aff_min_payout_options')));	
        }
                          
        echo "DONE<br/>";
    }
    
    private function migrateTrackingAndCookies() {
    	echo "&nbsp;&nbsp;Migrating tracking & cookies settings.....";

    	// link method
    	Gpf_Settings::set(Pap_Settings::SETTING_LINKING_METHOD,
    						($this->getPap3Setting('Aff_link_style') == 1 ? Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT : Pap_Tracking_ClickTracker::LINKMETHOD_URLPARAMETERS));
    	
    	// tracking by IP
    	Gpf_Settings::set(Pap_Settings::TRACK_BY_IP_SETTING_NAME,
    						$this->convert1ToY($this->getPap3Setting('Aff_track_by_ip')));
    	Gpf_Settings::set(Pap_Settings::IP_VALIDITY_SETTING_NAME,
    						$this->getPap3Setting('Aff_ip_validity'));
    	Gpf_Settings::set(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME,
    						Pap3Compatibility_Migration_Pap3Constants::translateIpValidity($this->getPap3Setting('Aff_ip_validity_type')));
    						

    	// save unreferred affiliate
    	if($this->getPap3Setting('Aff_referred_affiliate') != '') {
    		Gpf_Settings::set(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME,
    							$this->convert1ToY($this->getPap3Setting('Aff_referred_affiliate_allow')));
    		Gpf_Settings::set(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME,
    							$this->getPap3Setting('Aff_referred_affiliate'));
    	}
    						
    	Gpf_Settings::set(Pap_Settings::URL_TO_P3P,
    						$this->getPap3Setting('Aff_p3p_xml'));
    	Gpf_Settings::set(Pap_Settings::P3P_POLICY_COMPACT,
    						$this->getPap3Setting('Aff_p3p_compact'));
    						
    	Gpf_Settings::set(Pap_Settings::OVERWRITE_COOKIE, 
    						$this->convert1ToY($this->getPap3Setting('Aff_overwrite_cookie')));
    	Gpf_Settings::set(Pap_Settings::DELETE_COOKIE, 
    						$this->convert1ToY($this->getPap3Setting('Aff_delete_cookie')));
    						
    	echo "DONE<br/>";
    }    
}
?>
