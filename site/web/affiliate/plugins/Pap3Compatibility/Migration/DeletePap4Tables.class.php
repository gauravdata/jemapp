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

class Pap3Compatibility_Migration_DeletePap4Tables extends Gpf_Object {
    
    public function run() {
    	$time1 = microtime();
    	Pap3Compatibility_Migration_OutputWriter::logOnce("Deleting migrated data from existing PAP4 tables<br/>");

    	try {
    		$this->deleteTable(Pap_Db_Table_Campaigns::getName());
    		$this->deleteTable(Pap_Db_Table_CommissionGroups::getName());
    		$this->deleteTable(Pap_Db_Table_CommissionTypes::getName());
    		$this->deleteTable(Pap_Db_Table_Commissions::getName());
    		$this->deleteTable(Pap_Db_Table_UserInCommissionGroup::getName());
    		
    		$this->deleteTable(Gpf_Db_Table_FormFields::getName());
    		$this->deleteTable(Gpf_Db_Table_FieldGroups::getName());
    		
    		$this->deleteTable(Pap_Db_Table_Transactions::getName());
    		$this->deleteTable(Pap_Db_Table_Clicks::getName());
    		$this->deleteTable(Pap_Db_Table_RawClicks::getName());
    		$this->deleteTable(Pap_Db_Table_Impressions::getName());

    		$this->deleteTable(Pap_Db_Table_Banners::getName());
    		
    		$this->deleteTable(Gpf_Db_Table_FieldGroups::getName());
    		$this->deleteTable(Pap_Db_Table_PayoutsHistory::getName());
    		$this->deleteTable(Pap_Db_Table_Payouts::getName());
    		
    		$this->deleteTable(Gpf_Db_Table_Currencies::getName());
    		$this->deleteTable(Gpf_Db_Table_MailAccounts::getName());
    		
    	} catch(Exception $e) {
    		Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;Errror: ".$e->getMessage()."<br/>");
    	}

    	$time2 = microtime();
		Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }
    
    private function deleteTable($tableName) {
    	Pap3Compatibility_Migration_OutputWriter::logOnce("&nbsp;&nbsp;Deleting data from table ".$tableName.".....");
    	$sql = new Gpf_SqlBuilder_DeleteBuilder();
    	$sql->from->add($tableName);
    	$sql->execute();
    	Pap3Compatibility_Migration_OutputWriter::log("DONE<br/>");
    }
}
/*
DROP TABLE `qu_g_accounts`, `qu_g_activeviews`, `qu_g_authusers`, `qu_g_countries`, `qu_g_currencies`, `qu_g_exports`, `qu_g_fieldgroups`,
`qu_g_filecontents`, `qu_g_files`, `qu_g_filters`, `qu_g_filter_conditions`, `qu_g_formfields`, `qu_g_gadgetproperties`, `qu_g_gadgets`,
`qu_g_importexport`, `qu_g_installedtemplates`, `qu_g_languages`, `qu_g_lang_sources`, `qu_g_lang_translations`, `qu_g_logins`, `qu_g_logs`,
`qu_g_mails`, `qu_g_mail_accounts`, `qu_g_mail_attachments`, `qu_g_mail_outbox`, `qu_g_mail_templates`, `qu_g_mail_template_attachments`,
`qu_g_passwd_requests`, `qu_g_plannedtasks`, `qu_g_recurrencepresets`, `qu_g_recurrencesettings`, `qu_g_roles`, `qu_g_rolesprivileges`,
`qu_g_sections`, `qu_g_settings`, `qu_g_tasks`, `qu_g_userattributes`, `qu_g_users`, `qu_g_versions`, `qu_g_views`, `qu_g_view_columns`,
`qu_g_wallpapers`, `qu_g_windows`, `qu_g_words`, `qu_pap_affiliatescreens`, `qu_pap_banners`, `qu_pap_campaigns`, `qu_pap_channels`,
`qu_pap_commissiongroups`, `qu_pap_commissions`, `qu_pap_commissiontypes`, `qu_pap_cpmcommissions`, `qu_pap_dailyclicks`, `qu_pap_dailyimpressions`,
`qu_pap_directlinkurls`, `qu_pap_lifetime_referrals`, `qu_pap_monthlyclicks`, `qu_pap_monthlyimpressions`, `qu_pap_payout`, `qu_pap_payouthistory`,
`qu_pap_rawclicks`, `qu_pap_transactions`, `qu_pap_userincommissiongroup`, `qu_pap_userpayoutoptions`, `qu_pap_users`, qu_g_recurrencepresets;
*/
?>
