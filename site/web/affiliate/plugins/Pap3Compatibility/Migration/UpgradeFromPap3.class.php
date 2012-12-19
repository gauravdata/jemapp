<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Pap3Compatibility_Migration_UpgradeFromPap3 extends Gpf_Tasks_LongTask {

    public function getName() {
        return $this->_('Upgrade from PAP3');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
		if($this->isPending('initOutput')) {
			Pap3Compatibility_Migration_OutputWriter::reset();
    	   	Pap3Compatibility_Migration_OutputWriter::logOnce("Starting migration<br/>");
			$this->setDone();
		} else {
			Pap3Compatibility_Migration_OutputWriter::initialize();
		}

		if($this->isPending('deleteFromTables')) {
    		$this->deleteFromTables();
			$this->setDone();
		}

		if($this->isPending('migrateCampaigns')) {
    		$this->migrateCampaigns();
    		$this->setDone();
		}

		if($this->isPending('migrateBanners')) {
			$this->migrateBanners();
			$this->setDone();
		}

        if($this->isPending('migratePayouts')) {
            $this->migratePayouts();
            $this->setDone();
        }

		if($this->isPending('migrateAffiliates')) {
			$this->migrateAffiliates();
			$this->setDone();
		}
		
		if($this->isPending('migrateSettings')) {
			$this->migrateSettings();
			$this->setDone();
		}

		if($this->isPending('migrateTransactions')) {
			$this->migrateTransactions();
			$this->setDone();
		}
   	}

    protected function deleteFromTables() {
    	$task = new Pap3Compatibility_Migration_DeletePap4Tables();
    	$task->run();
    }

    protected function migrateCampaigns() {
    	$task = new Pap3Compatibility_Migration_TaskCampaigns();
    	$task->run();
    }

    protected function migrateAffiliates() {
    	$task = new Pap3Compatibility_Migration_TaskAffiliatesData();
    	$task->run();
    }

    protected function migrateTransactions() {
    	$task = new Pap3Compatibility_Migration_TaskTransactions();
    	$task->run();
    }

    protected function migrateBanners() {
    	$task = new Pap3Compatibility_Migration_TaskBanners();
    	$task->run();
    }

    protected function migratePayouts() {
    	$task = new Pap3Compatibility_Migration_TaskPayouts();
    	$task->run();
    }

    protected function migrateSettings() {
    	$task = new Pap3Compatibility_Migration_TaskSettings();
    	$task->run();
    }
}

/*
DROP TABLE qu_g_accounts, qu_g_activeviews, qu_g_authusers, qu_g_countries, qu_g_currencies, qu_g_exports, qu_g_fieldgroups,
qu_g_filecontents, qu_g_files, qu_g_filters, qu_g_filter_conditions, qu_g_formfields, qu_g_gadgetproperties, qu_g_gadgets,
qu_g_importexport, qu_g_installedtemplates, qu_g_languages, qu_g_lang_sources, qu_g_lang_translations, qu_g_logins, qu_g_logs,
qu_g_mails, qu_g_mail_accounts, qu_g_mail_attachments, qu_g_mail_outbox, qu_g_mail_templates, qu_g_mail_template_attachments,
qu_g_passwd_requests, qu_g_plannedtasks, qu_g_recurrencepresets, qu_g_recurrencesettings, qu_g_roles, qu_g_rolesprivileges,
qu_g_sections, qu_g_settings, qu_g_tasks, qu_g_userattributes, qu_g_users, qu_g_versions, qu_g_views, qu_g_view_columns,
qu_g_wallpapers, qu_g_windows, qu_g_words, qu_pap_affiliatescreens, qu_pap_banners, qu_pap_bannersinrotators, qu_pap_campaigns,
qu_pap_channels, qu_pap_commissiongroups, qu_pap_commissions, qu_pap_commissiontypes, qu_pap_cpmcommissions, qu_pap_dailyclicks,
qu_pap_dailyimpressions, qu_pap_directlinkurls, qu_pap_lifetime_referrals, qu_pap_monthlyclicks, qu_pap_monthlyimpressions,
qu_pap_payout, qu_pap_payouthistory, qu_pap_rawclicks, qu_pap_rules, qu_pap_transactions, qu_pap_userincommissiongroup,
qu_pap_userpayoutoptions, qu_pap_users;
*/
?>
