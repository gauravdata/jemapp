<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */

class TopLevelAffiliateFixedCommision_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'TopLevelAffiliateFixedCommision';
        $this->name = $this->_('Top Level Affiliate Fixed Commision');
        $this->description = $this->_('Top level affiliate takes fixed percent of commission in each sale. It is not recommended to use this plugin with activated Top Level Affiliate Commision! '.
        'If you want to setup this plugin correctly, visit our <a href="%s" target="_blank">Knowledgebase</a>.', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/top-level-affiliate-fixed-commission.html'));
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.2.0.14');

        $this->addImplementation('Tracker.saveAllCommissions', 'TopLevelAffiliateFixedCommision_Main', 'modifyCommission');
        $this->addImplementation('Core.defineSettings', 'TopLevelAffiliateFixedCommision_Main', 'initSettings');
        
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.initFields', 
        	'TopLevelAffiliateFixedCommision_Config', 'initFieldsInForm');
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.save',
            'TopLevelAffiliateFixedCommision_Config', 'save');
        $this->addImplementation('PostAffiliate.CommissionTypeEditAdditionalForm.load',
            'TopLevelAffiliateFixedCommision_Config', 'load');
    }
    
    public function onActivate() {
        if (!Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive(Pap_Features_CommissionGroups_Definition::CODE_NAME)) {
            throw new Gpf_Exception($this->_('Plugin can not be activated because Commissions Group feature is not activated!'));
        }
    }
}

?>
