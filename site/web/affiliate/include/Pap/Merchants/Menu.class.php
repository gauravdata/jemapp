<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Menu.class.php 32430 2011-05-10 10:44:03Z mkendera $
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
class Pap_Merchants_Menu extends Gpf_Menu {

	public function createMenu() {
		$this->addItem('Home', $this->_('Home'));

		$item = $this->addItem('Campaigns-Overview', $this->_('Campaigns'));
		$item->addItem('Campaigns-Overview', $this->_('Overview'));
		$item->addItem('Campaigns-Manager', $this->_('Campaigns Manager'));

		$item = $this->addItem('Banners-Overview', $this->_('Banners'));
		$item->addItem('Banners-Overview', $this->_('Overview'));
		$item->addItem('Banner-Manager', $this->_('Banners Manager'));

		$item = $this->addItem('Affiliates-Overview', $this->_('Affiliates'));
		$item->addItem('Affiliates-Overview', $this->_('Overview'));
		$item->addItem('Affiliate-Tree', $this->_('Affiliate tree'));
		$item->addItem('Affiliate-Manager', $this->_('Affiliates Manager'));
		$item->addItem('Direct-Links-Manager', $this->_('DirectLinks Urls'));

		$item = $this->addItem('Transactions-Overview', $this->_('Transactions'));
		$item->addItem('Transactions-Overview', $this->_('Overview'));
		$item->addItem('Clicks-List', $this->_('Raw clicks list'));
		$item->addItem('Transaction-Manager', $this->_('Commissions'));

		$item = $this->addItem('Reports', $this->_('Reports'));
		$item->addItem('Reports', $this->_('Overview'));
		$item->addItem('Quick-Report', $this->_('Quick report'));
		$item->addItem('Trends-Report', $this->_('Trends report'));
		$item->addItem('Daily-Report', $this->_('Daily report'));
		$item->addItem('Transaction-Manager', $this->_('Commissions'));
		$item->addItem('Clicks-List', $this->_('Raw clicks list'));
		$item->addItem('Payouts-History', $this->_('Payouts history'));
		$item->addItem('Payouts-By-Affiliate', $this->_('Payouts by affiliate'));
		$item->addItem('Online-Users', $this->_('Online users'));
		$item->addItem('Top-Affiliates', $this->_('Top affiliates'));
		$item->addItem('Top-referring-URLs', $this->_('Top referring URLs'));

		$item = $this->addItem('Payouts', $this->_('Payouts'));
		$item->addItem('Payouts', $this->_('Overview'));
		$item->addItem('Pay-Affiliates', $this->_('Pay affiliates'));

		$item = $this->addItem('Communication', $this->_('Emails'));
		$item->addItem('Communication', $this->_('Overview'));
		$item->addItem('Send-Message', $this->_('Send message'));
		$item->addItem('Mail-Outbox', $this->_('Mail outbox'));

		$this->addItem('Configuration-Manager', $this->_('Configuration'));

		$item = $this->addItem('Tools', $this->_('Tools'));
		$item->addItem('Tools', $this->_('Overview'));
		$item->addItem('Integration', $this->_('Integration'));
		$item->addItem('Logs-History', $this->_('Event logs'));
		$item->addItem('Logins-History', $this->_('Logins history'));
		$item->addItem('Import-Export', $this->_('Import / Export'));
		$item->addItem('Getting-Started', $this->_('Getting started'));
		$item->addItem('Visitor-Affiliates', $this->_('Visitor affiliates'));
		$item->addItem('Views', $this->_('Views'));
		$item->addItem('ReportProblems', $this->_('Report problems'));

		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.merchant.menu', $this);
	}

	/**
	 * Add privileges for overview screens. It is used only in client side code.
	 *
	 * @service campaigns_overview read
	 * @service banners_overview read
	 * @service affiliates_overview read
	 * @service transactions_overview read
	 * @service reports_overview read
	 * @service payouts_overview read
	 * @service configuration_overview read
	 * @service troubleshooting read
	 * @service click_integration read
	 * @service integration_overview read
	 * @service communication_overview read
	 * @service tools_overview read
	 * @service subid_tracking read
	 * @service promotion_overview read
	 * @service advanced_functionality read
	 */
}

?>
