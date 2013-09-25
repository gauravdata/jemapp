<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package ShopMachine
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
class Pap_Mail_SaleMail extends Pap_Mail_UserMail {

    /**
     * @var Pap_Common_Transaction
     */
    protected $transaction;

    public function __construct() {
        parent::__construct();
        $this->isHtmlMail = true;
    }

    protected function initTemplateVariables() {
        parent::initTemplateVariables();
        $transactionFields = Pap_Common_TransactionFields::getInstance();
        $fields = $transactionFields->getTransactionFields();
        foreach ($fields as $code => $title) {
            $this->addVariable($code, $title);
        }
    }

    protected function setVariableValues() {
        parent::setVariableValues();

        $transactionFields = Pap_Common_TransactionFields::getInstance();
        $this->updateTransactionFields($transactionFields);
        $transactionValues = $transactionFields->getTransactionFieldsValues();
        foreach($transactionValues as $code => $value) {
            $this->setVariable($code, $value);
        }
    }
    
    public function updateTransactionFields($transactionFields) {
        $transactionFields->setTransaction($this->transaction);
    }

    
    protected function setTimeVariableValues($timeOffset = 0) {
        parent::setTimeVariableValues($timeOffset);
        $firstClickTime = Gpf_Common_DateUtils::getTimestamp($this->transaction->get(Pap_Db_Table_Transactions::FIRST_CLICK_TIME));
        $lastClickTime = Gpf_Common_DateUtils::getTimestamp($this->transaction->get(Pap_Db_Table_Transactions::LAST_CLICK_TIME));
        $this->setVariable('firstclicktime', Gpf_Common_DateUtils::getDateTime($firstClickTime + $timeOffset));
        $this->setVariable('lastclicktime', Gpf_Common_DateUtils::getDateTime($lastClickTime + $timeOffset));
    }

    public function setTransaction(Pap_Common_Transaction $transaction) {
        $this->transaction = $transaction;
    }
}
