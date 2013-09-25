<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
abstract class Pap_Mail_Reports_Report extends Pap_Mail_UserMail {

	/**
	 * @var Pap_Stats_Params
	 */
	private $timeOffset;
	protected $dateFrom;
	protected $dateTo;

	public function __construct(Pap_Stats_Params $statsParams = null) {
		parent::__construct($statsParams);
		$this->initTemplate();
		$this->isHtmlMail = true;
	}
	
    public function setAccountId($accountId) {
    	Gpf_Plugins_Engine::extensionPoint('PostAffiliatePro.Pap_Mail_Reports_Report.setAccountId', 
    		new Gpf_Data_Record(array('accountId', 'statsParams'), array($accountId, $this->statsParams)));    	
    }

	protected abstract function initDate(Pap_Stats_Params $statsParams, $timeOffset);
	
	protected abstract function initTemplate();

	protected function initUserVariables() {
		parent::initTemplateVariables();
	}
	
	protected function setUserVariables() {
		parent::setVariableValues();
	}
	
	protected function initTemplateVariables() {
		$this->addVariable('currency', $this->_("Currency"));
		$this->addVariable('dateFrom', $this->_("Date from is report generated"));
		$this->addVariable('dateTo', $this->_("Date to is report generated"));

		$this->addVariable('commissionsList->list', $this->_("List of commissions"));
		$this->addVariable('salesList->list', $this->_("List of sales"));
		$this->addVariable('actionsList->list', $this->_("List of actions"));
	}

	protected function setTimeVariableValues($timeOffset = 0) {
		$this->timeOffset = $timeOffset;
		parent::setTimeVariableValues($this->timeOffset);
		$this->initDate($this->statsParams, $this->timeOffset);
		$this->setVariable('dateFrom', Gpf_Common_DateUtils::getDateInLocaleFormat(strtotime($this->dateFrom) + $this->timeOffset));
		$this->setVariable('dateTo', Gpf_Common_DateUtils::getDateInLocaleFormat(strtotime($this->dateTo) + $this->timeOffset));
	}

	protected function setVariableValues() {
	    $this->setVariable('currency', Pap_Common_Utils_CurrencyUtils::getDefaultCurrency()->getSymbol());

	    $this->setStatsVariables();    
	    
	    $this->setVariableRaw('commissionsList', new Pap_Mail_Reports_List($this->createTransactionList($this->statsParams)));
		$this->setVariableRaw('salesList', new Pap_Mail_Reports_List($this->getSalesList()));
		$this->setVariableRaw('actionsList', new Pap_Mail_Reports_List($this->getActionsList()));
	}
	
	/**
	 * @return Pap_Mail_Reports_HasHtml
	 */
	private function getSalesList() {
		$statsParams = clone $this->statsParams;
		$statsParams->setType(Pap_Common_Constants::TYPE_SALE);
		return $this->createTransactionList($statsParams);
	}
	
	/**
	 * @return Pap_Mail_Reports_HasHtml
	 */
	private function getActionsList() {
		$statsParams = clone $this->statsParams;
		$statsParams->setType(Pap_Common_Constants::TYPE_ACTION);
		return $this->createTransactionList($statsParams);
	}
	
	/**
	 * @param $statsparams
	 * @return Pap_Mail_Reports_HasHtml
	 */
	protected function createTransactionList(Pap_Stats_Params $statsParams) {
		return new Pap_Mail_Reports_TransactionsListProvider($statsParams, $this->timeOffset);
	}

}
