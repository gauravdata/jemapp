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
class Pap_Features_MultipleCurrency_Definition extends Gpf_Plugins_Definition {

	public function __construct() {
		$this->codeName =  'MultipleCurrencies';
		$this->name = $this->_('Multiple currencies');
		$this->description = $this->_('Multiple currencies allow you to track sales in multiple currencies.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/multiple-currencies.html'));
		$this->version = '1.0.0';
		$this->pluginType = self::PLUGIN_TYPE_FEATURE;
		
		$this->addImplementation('PostAffiliate.CurrencyForm.save', 'Pap_Features_MultipleCurrency_Main', 'currencySave');
		$this->addImplementation('PostAffiliate.CurrencyForm.load', 'Pap_Features_MultipleCurrency_Main', 'currencyLoad');
	    $this->addImplementation('Tracker.action.computeTotalCost', 'Pap_Features_MultipleCurrency_Main', 'computeTotalCost');
	    $this->addImplementation('Tracker.action.computeFixedCost', 'Pap_Features_MultipleCurrency_Main', 'computeFixedCost');
	    $this->addImplementation('Tracker.action.computeCommission', 'Pap_Features_MultipleCurrency_Main', 'computeCommission');
	    
	}
	
    public function onDeactivate() {
    	Gpf_Settings::set(Pap_Settings::MULTIPLE_CURRENCIES, 'N');
    }
}

?>
