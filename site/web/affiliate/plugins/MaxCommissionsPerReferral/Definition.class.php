<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class MaxCommissionsPerReferral_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'MaxCommissionsPerReferral';
        $this->name = $this->_('Maximum Commissions Per Referral');
        $this->description = $this->_('Enables to set maximum number of commissions per user that will be saved. <a href="%s" target="_blank">More help in our Knowledge Base</a>.', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/how-to-set-maximum-commission-per-referral.html'));
        $this->version = '1.0.0';
        $this->help = $this->_('This plugin enables to set limit of maximum number of commissions per user(customer) in time interval(seconds) that will be saved.');

        $this->addRequirement('PapCore', '4.0.4.6');

        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.initFields',
            'MaxCommissionsPerReferral_Main', 'initFields');
        
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.save',
            'MaxCommissionsPerReferral_Main', 'save');
        
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.load',
            'MaxCommissionsPerReferral_Main', 'load');
        
        $this->addImplementation('Tracker.action.beforeSaveCommissions',
            'MaxCommissionsPerReferral_Main', 'setSaveCommission');

        $this->addImplementation('Tracker.click.beforeSaveCommissions',
            'MaxCommissionsPerReferral_Main', 'setSaveClickCommission');
    }
}
?>
