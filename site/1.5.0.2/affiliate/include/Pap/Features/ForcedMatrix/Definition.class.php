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

class Pap_Features_ForcedMatrix_Definition extends Gpf_Plugins_Definition  {
	public function __construct() {
		$this->codeName = 'ForcedMatrix';
		$this->name = $this->_('Forced matrix');
		$this->description = $this->_('Forced matrix allows you to limit to the number of referrals of subaffiliates any affiliate can refer. It means that every affiliate can have only specified number of children. <a href="%s" target="_blank">More details read in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/forced-matrix.html'));
		$this->version = '1.0.0';
		$this->pluginType = self::PLUGIN_TYPE_FEATURE;

		$this->addImplementation('PostAffiliate.AffiliateSignupForm.save', 'Pap_Features_ForcedMatrix_Main', 'save');
		$this->addImplementation('PostAffiliate.AffiliateSignupForm.load', 'Pap_Features_ForcedMatrix_Main', 'load');
        $this->addImplementation('PostAffiliate.AffiliateForm.fillAdd', 'Pap_Features_ForcedMatrix_Main', 'computeParentFor');
        $this->addImplementation('PostAffiliate.AffiliateForm.afterSave', 'Pap_Features_ForcedMatrix_Main', 'processFillBonus');        
        $this->addImplementation('PostAffiliate.AffiliateForm.assignParent', 'Pap_Features_ForcedMatrix_Main', 'computeParentForUnrefferedAffiliate');
        $this->addImplementation('PostAffiliate.Features.PerformanceRewards.Action.createActionList', 'Pap_Features_ForcedMatrix_Main', 'addAction');
	}
}
?>
