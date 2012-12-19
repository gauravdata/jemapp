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

/**
 * @package PostAffiliatePro
 */
class SaleFraudProtection_Definition extends Gpf_Plugins_Definition {
    public function __construct() {
        $this->codeName =  'SaleFraudProtection';
        $this->name = $this->_('Sale Tracking Fraud Protection');
        $this->description = $this->_('Sale will be tracked <b>ONLY</b> if sale fraud protection parameter is correctly set. If you want to setup this plugin correctly, visit our <a href="%s" target="_blank">Knowledgebase</a>.', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/sale-tracking-fraud-protection.html'));
        $this->version = '1.0.0';               
        $this->addRequirement('PapCore', '4.1.10.1');
        $this->configurationClassName = 'SaleFraudProtection_Config';
        
        $this->addImplementation('Core.defineSettings', 'SaleFraudProtection_Main', 'initSettings');
        $this->addImplementation('Tracker.action.beforeSaveCommissions', 'SaleFraudProtection_Main', 'process');
		$this->addImplementation('PostAffiliate.OfflineSaleForm.load', 'SaleFraudProtection_Main', 'loadCoupon');
		$this->addImplementation('PostAffiliate.OfflineSaleForm.createSale', 'SaleFraudProtection_Main', 'createOfflineSale');
    }
}

?>
