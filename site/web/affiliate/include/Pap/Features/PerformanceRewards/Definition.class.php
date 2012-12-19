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

class Pap_Features_PerformanceRewards_Definition extends Gpf_Plugins_Definition  {

    const CODE =  'PerformanceRewards';

    public function __construct() {
        $this->codeName = self::CODE;
        $this->name = $this->_('Performance rewards');
        $this->description = $this->_('Performance rewards is a powerful featuure that allows you to reward your affiliates for their performance.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/performance-rewards.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.Transaction.afterSave', 'Pap_Features_PerformanceRewards_Main', 'checkRules');
    }
}
?>
