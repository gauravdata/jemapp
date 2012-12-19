<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
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
class Pap_Tracking_Action_RecognizeCurrency extends Gpf_Object {
	
    public function processTotalCost (Pap_Contexts_Action $context) {
    	return $this->computeTotalCost($context);
    }
    
	public function processFixedCost (Pap_Contexts_Action $context) {
    	return $this->computeFixedCost($context);
    }
    
	public function processCommission (Pap_Contexts_Action $context) {
    	return $this->computeCustomCommission($context);
    }
    
    public function computeCustomCommission(Pap_Contexts_Action $context) {
    	$context->debug('Recognizing commission currency started');

    	$defaultCurrency = $this->getDefaultCurrency();
    	$context->debug("    Default currency is ".$defaultCurrency->getName());
    	$context->set("defaultCurrencyObject", $defaultCurrency);
    	if ($context->getCurrencyFromRequest() != '') {
    		Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeCommission', $context);
    	}
    	$context->debug('Recognizing commission currency ended');
		$context->debug("");
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    private function computeTotalCost(Pap_Contexts_Action $context) {
    	$context->debug('Recognizing totalCost currency started');

    	$defaultCurrency = $this->getDefaultCurrency();
    	$context->debug("    Default currency is ".$defaultCurrency->getName());
        $context->set("defaultCurrencyObject", $defaultCurrency);

        $context->setRealTotalCost($context->getTotalCostFromRequest());
        $context->debug('Setting realTotalCost to '.$context->getTotalCostFromRequest());
        if ($context->getCurrencyFromRequest() != '') {
            Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeTotalCost', $context);
        }
    	
    	$context->debug('Recognizing totalCost currency ended. totalCost: '.$context->getRealTotalCost());
		$context->debug("");
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    private function computeFixedCost(Pap_Contexts_Action $context) {
    	$context->debug('Recognizing fixedCost currency started');

    	$defaultCurrency = $this->getDefaultCurrency();
    	$context->debug("    Default currency is ".$defaultCurrency->getName());
    	$context->set("defaultCurrencyObject", $defaultCurrency);
    	
    	if ($context->getCurrencyFromRequest() != '') {
    		Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeFixedCost', $context);
    	}
    	
    	$context->debug('Recognizing fixedCost currency ended');
		$context->debug("");
    	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    /**
     * retrieves default currency
     *
     * @return Gpf_Db_Currency
     */
    private function getDefaultCurrency() {
        try {
            return Gpf_Db_Currency::getDefaultCurrency();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Pap_Tracking_Exception("    Critical error - No default currency is defined");
        }
    }
}

?>
