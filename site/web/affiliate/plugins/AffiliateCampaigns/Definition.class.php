<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
class AffiliateCampaigns_Definition extends Gpf_Plugins_Definition {

	public function __construct() {
		$this->codeName =  'AffiliateCampaigns';
		$this->name = $this->_('Affiliate campaigns');
		$this->description = $this->_('Enable variable {$affiliatecampaigns} in email templates. This variable will contain associative array of all user campaigns. <a href="%s" target="_blank">%s</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/affiliate-campaigns-plugin.html'), $this->_('Read more in our Knowledge Base'));
		$this->version = '1.0.0';
		$this->addRequirement('PapCore', '4.2.5.0');

		$this->addImplementation('PostAffiliate.UserMail.initTemplateVariables', 'AffiliateCampaigns_Main', 'initCampaignsVariable');
		$this->addImplementation('PostAffiliate.UserMail.setVariableValues', 'AffiliateCampaigns_Main', 'setCampaignsVariable');
	}
}
?>
