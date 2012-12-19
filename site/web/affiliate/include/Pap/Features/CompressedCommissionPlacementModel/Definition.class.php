<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_CompressedCommissionPlacementModel_Definition extends Gpf_Plugins_Definition  {

    const PROCESSING_ENABLED = 'compressedCommissionAutomaticProcessingEnabled';
    const RECURRENCE = 'compressedCommissionRecurrence';
    const ACTION = 'compressedCommissionAction';
    const DAY = 'compressedCommissionDay';
    const RULE_WHAT = 'compressedCommissionRuleWhat';
    const RULE_STATUS = 'compressedCommissionRuleStatus';
    const RULE_EQUATION = 'compressedCommissionRuleEquation';
    const RULE_EQUATION_VALUE1 = 'compressedCommissionRuleEquationValue1';
    const RULE_EQUATION_VALUE2 = 'compressedCommissionRuleEquationValue2';

    const RECURRENCE_WEEKLY = 'w';
    const RECURRENCE_MONTHLY = 'm';


    public function __construct() {
        $this->codeName = 'CompressedCommissionPlacementModel';
        $this->name = $this->_('Compressed commission placement model');
        $this->description = $this->_('This feature applies compressed commission placement model.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>',Gpf_Application::getKnowledgeHelpUrl('features-and-functions/compressed-commission-placement-model.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.merchant.menu', 'Pap_Features_CompressedCommissionPlacementModel_Main', 'addToMenu');
        $this->addImplementation('Core.defineSettings', 'Pap_Features_CompressedCommissionPlacementModel_Main', 'initSettings');
    }
}
?>
