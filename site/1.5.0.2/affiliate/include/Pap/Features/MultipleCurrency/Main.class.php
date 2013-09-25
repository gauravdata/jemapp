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
class Pap_Features_MultipleCurrency_Main extends Gpf_Plugins_Handler {

	/**
	 * @return Pap_Features_MultipleCurrency_Main
	 */
	public static function getHandlerInstance() {
		return new Pap_Features_MultipleCurrency_Main();
	}

	public function currencySave(Gpf_Rpc_Form $form) {
		Gpf_Settings::set(Pap_Settings::MULTIPLE_CURRENCIES, $form->getFieldValue('multiple_currencies'));

		return Gpf_Plugins_Engine::PROCESS_CONTINUE;
	}

	public function currencyLoad(Gpf_Rpc_Form $form) {
		try {
			$form->setField('multiple_currencies', Gpf_Settings::get(Pap_Settings::MULTIPLE_CURRENCIES));
		} catch (Gpf_Exception $e) {
			$form->setField('multiple_currencies', 'N');
		}
		return Gpf_Plugins_Engine::PROCESS_CONTINUE;
	}

	/*public function recognizeCurrency(Pap_Contexts_Action $context) {
		if (Gpf_Settings::get(Pap_Settings::MULTIPLE_CURRENCIES) == 'N') {
			return;
		}

		$context->debug('Multiple currencies started');

		try {
			$this->process($context);
		} catch (Gpf_Exception $e) {
			$context->debug('STOPING with exception: ' . $e->getMessage());
		}

		$context->debug('Multiple currencies ended');
	}*/

	
	
	public function computeTotalCost(Pap_Contexts_Action $context) {
		if (Gpf_Settings::get(Pap_Settings::MULTIPLE_CURRENCIES) == 'N') {
		    $context->debug('Multiple currencies are not allowed');
			return;
		}
		
		$context->debug('Multiple currencies started');
		
		try {
			$currency = $this->process($context);
		} catch (Gpf_Exception $e) {
			$context->debug('STOPING with exception: ' . $e->getMessage());
			return;
		}
		
		
		$totalCost = $context->getTotalCostFromRequest();
		$context->debug('total cost recomputed from '.$totalCost.' to '.$totalCost * $currency->getExchangeRate());
		$context->setRealTotalCost($totalCost * $currency->getExchangeRate());
		$this->setOriginalCurrencyValues($context->getTransaction(), $currency, $totalCost);

		$context->debug('Total cost was succesfully recomputed');
		
		$context->debug('Multiple currencies ended');
	}
	
	public function computeFixedCost(Pap_Contexts_Action $context) {
		$context->debug('MC fixed cost');
		if (Gpf_Settings::get(Pap_Settings::MULTIPLE_CURRENCIES) == 'N') {
			return;
		}
		
		$context->debug('Multiple currencies started');
		
		try {
			$currency = $this->process($context);
		} catch (Gpf_Exception $e) {
			$context->debug('STOPING with exception: ' . $e->getMessage());
			return;
		}
		
		$fixedCost = $context->getFixedCostFromRequest();
		$context->debug('fixed cost recomputed from '.$fixedCost.' to '.$fixedCost * $currency->getExchangeRate());
		$context->setFixedCost($fixedCost * $currency->getExchangeRate());
		
		$this->setOriginalCurrencyValues($context->getTransaction(), $currency, $fixedCost);

		$context->debug('Fixed cost was succesfully recomputed');
	}
	
	public function computeCommission(Pap_Contexts_Action $context) {
		$context->debug('MC commision');
		if (Gpf_Settings::get(Pap_Settings::MULTIPLE_CURRENCIES) == 'N') {
			return;
		}
		
		$context->debug('Multiple currencies started');
		
		try {
			$currency = $this->process($context);
		} catch (Gpf_Exception $e) {
			$context->debug('STOPING with exception: ' . $e->getMessage());
			return;
		}
		
		$commissionValue = $context->getCommission(1,Pap_Db_Table_Commissions::SUBTYPE_NORMAL)->getValue();
		$commissionType = $context->getCommission(1,Pap_Db_Table_Commissions::SUBTYPE_NORMAL)->getType();
		
		$context->debug('commission recomputed from '.$commissionValue.' to '.$commissionValue * $currency->getExchangeRate());
		$context->removeCommission(1);
		$context->addCommission(new Pap_Tracking_Common_Commission(1, $commissionType, $commissionValue * $currency->getExchangeRate()));
		
		$this->setOriginalCurrencyValues($context->getTransaction(), $currency, $commissionValue * $currency->getExchangeRate());

		$context->debug('commision was succesfully recomputed');
	}

	private function process(Pap_Contexts_Action $context) {
		$currencyCode = $context->getCurrencyFromRequest();
		//$context->debug('Curr. from request: '.$currencyCode);
		if ($currencyCode == '') {
			throw new Gpf_Exception('Currency code is not defined');
		}

		if ($currencyCode == $context->getDefaultCurrencyObject()->getName()) {
			throw new Gpf_Exception('Currency is the same as default currency');
		}

		$currency = new Gpf_Db_Currency();
		try {
			$currency = $currency->findCurrencyByCode($currencyCode);
		} catch (Gpf_DbEngine_NoRowException $e) {
			throw new Gpf_Exception('Currency does not exist');
		}
		return $currency;
	}
	
	private function setOriginalCurrencyValues(Pap_Common_Transaction $transaction, Gpf_Db_Currency $currency, $totalCost) {
		$transaction->setOriginalCurrencyId($currency->getId());
		$transaction->setOriginalCurrencyValue($totalCost);
		$transaction->setOriginalCurrencyRate($currency->getExchangeRate());
	}
}
?>
