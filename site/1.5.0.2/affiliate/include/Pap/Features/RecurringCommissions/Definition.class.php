<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_RecurringCommissions_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'RecurringCommissions';
        $this->name = $this->_('Recurring commissions');
        $this->description = $this->_('Recurring commissions allow you to reward your affiliates for recurring payments, for example for hosting or membership. <br/><a href="%s" target="_blank">More help in our Knowledge Base</a>',Gpf_Application::getKnowledgeHelpUrl('features-and-functions/recurring-commissions.html'));
        $this->version = '1.0.0';
        $this->configurationClassName = 'Pap_Features_RecurringCommissions_Config';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.CommissionTypeForm.loadAllCommissions',
                                 'Pap_Features_RecurringCommissions_Main', 'loadRecurringCommissionsToForm');
        $this->addImplementation('PostAffiliate.CommissionTypeForm.saveAllCommissions',
                                 'Pap_Features_RecurringCommissions_Main', 'saveRecurringCommissionsFromForm');        
        $this->addImplementation('Tracker.saveCommissions',
                                 'Pap_Features_RecurringCommissions_Main', 'saveCommissions');
        $this->addImplementation('PostAffiliate.merchant.menu',
                                 'Pap_Features_RecurringCommissions_Main', 'addToMenu');
        $this->addImplementation('PostAffiliate.Stats.initTransactionTypes', 'Pap_Features_RecurringCommissions_Main', 
        						 'initTransactionTypes');
        $this->addImplementation('PostAffiliate.StatisticsBase.initDataTypes', 'Pap_Features_RecurringCommissions_Main', 
        						 'initDataTypes');
    }
    
    public function onDeactivate() {
        $this->disableRecurringCommissionInCampaigns();
        
        $config = new Pap_Features_RecurringCommissions_Config();
        $config->deleteRecurringCommissionsTask();
    }
    
    private function disableRecurringCommissionInCampaigns() {
        Pap_Db_Table_Commissions::getInstance()->deleteAllSubtypeCommissions(Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
    }
    
    public function onActivate() {
        $taskRunner = new Gpf_Tasks_Runner();
        if (!$taskRunner->isRunningOK()) {
            throw new Gpf_Exception($this->_('Recurring commissions require cron job which is not running now. Please set it up in Tools -> Integration -> Cron Job Integration'));
        }
        
        $config = new Pap_Features_RecurringCommissions_Config();
        $config->addRecurringCommissionsTask();
    }
}
?>
