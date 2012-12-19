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
class Pap_Mail_UserMail extends Gpf_Mail_Template {

    const PARENT_PREFFIX = 'parent_';

    /**
     * @var Pap_Common_User
     */
    protected $user;

    /**
     * @var Pap_Stats_Params
     */
    protected $statsParams;

    public function __construct(Pap_Stats_Params $statsParams = null) {
        if (is_null($statsParams)) {
            $this->statsParams = new Pap_Stats_Params();
        } else {
            $this->statsParams = $statsParams;
            
        }
        
        parent::__construct();
        $this->isHtmlMail = true;
    }

    protected function initTemplateVariables() {
        $this->addVariable('userid', $this->_("Affiliate ID"));
        $this->addUserCustomFields();
        $this->initCommonVariables();
        $this->initMerchantVariables();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UserMail.initTemplateVariables', $this);
    }

    protected function initCommonVariables(){
        $this->addVariable('date', $this->_("Date"));
        $this->addVariable('time', $this->_("Time"));
        $this->addVariable('affiliateLoginUrl', $this->_("Affiliate Login Url"));
        $this->addVariable('affiliateLoginLink', $this->_("Affiliate Login Link"));
        $this->addVariable('merchantLoginUrl', $this->_("Merchant Login Url"));
        $this->addVariable('merchantLoginLink', $this->_("Merchant Login Link"));
        $this->addVariable('defaultCurrency', $this->_("Default Currency"));
}
    
    protected function initMerchantVariables(){
        $this->addVariable('impressions->count->raw', $this->_("Impressions raw"));
        $this->addVariable('impressions->count->unique', $this->_("Impressions unique"));
        
        $this->addVariable('clicks->count->raw', $this->_("Clicks raw"));
        $this->addVariable('clicks->count->unique', $this->_("Clicks unique"));

        $this->addVariable('sales->count->approved', $this->_("Number of Sales approved"));
        $this->addVariable('sales->count->pending', $this->_("Number of Sales pending"));
        $this->addVariable('sales->count->declined', $this->_("Number of Sales declined"));
        $this->addVariable('sales->count->paid', $this->_("Number of Sales paid"));
        $this->addVariable('sales->commission->all', $this->_("Commissions per Sales"));

        $this->addVariable('sales->totalCost->approved|currency', $this->_("Approved total cost of Sales"));
        $this->addVariable('sales->totalCost->pending|currency', $this->_("Pending total cost of Sales"));
        $this->addVariable('sales->totalCost->declined|currency', $this->_("Declined total cost of Sales"));
        $this->addVariable('sales->totalCost->paid|currency', $this->_("Paid total cost of Sales"));

        $this->addVariable('actions->count->all', $this->_("Number of Actions"));
        $this->addVariable('actions->totalCost->all', $this->_("Total cost of Actions"));
        $this->addVariable('actions->commission->all', $this->_("Comissions per Actions"));

        $this->addVariable('transactions->commission->approved|currency', $this->_("Commissions approved"));
        $this->addVariable('transactions->commission->pending|currency', $this->_("Commissions pending"));
        $this->addVariable('transactions->commission->declined|currency', $this->_("Commissions declined"));
        $this->addVariable('transactions->commission->paid|currency', $this->_("Commissions paid"));
        
        $this->addVariable('transactionsTier->commission->approved|currency', $this->_("Commissions approved multitier"));
        $this->addVariable('transactionsTier->commission->pending|currency', $this->_("Commissions pending multitier"));
        $this->addVariable('transactionsTier->commission->declined|currency', $this->_("Commissions declined multitier"));
        $this->addVariable('transactionsTier->commission->paid|currency', $this->_("Commissions paid multitier"));
    }
        
    protected function setVariableValues() {
        $this->setUserVariables();

        //Affiliate URL variables
        $affiliateLoginUrl = Gpf_Paths::getInstance()->getFullBaseServerUrl() . 'affiliates/login.php';
        $this->setVariable('affiliateLoginUrl', $affiliateLoginUrl);
        $this->setVariable('affiliateLoginLink', '<a href="' .
        $affiliateLoginUrl .'">' . $this->_('Affiliate login') . '</a>');

        $this->setStatsVariables();

        //Merchant URL variables
        $merchantLoginUrl = Gpf_Paths::getInstance()->getFullBaseServerUrl() . 'merchants/login.php';
        $this->setVariable('merchantLoginUrl', $merchantLoginUrl);
        $this->setVariable('merchantLoginLink', '<a href="' .
        $merchantLoginUrl . '">' . $this->_('Merchant login') . '</a>');

        $this->setVariable('defaultCurrency', $this->getDefaultCurrencySymbol());

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UserMail.setVariableValues', $this);
    }
    
    protected function setStatsVariables() {
        $this->setVariableRaw('impressions', new Pap_Stats_Impressions($this->statsParams));
        $this->setVariableRaw('clicks', new Pap_Stats_Clicks($this->statsParams));
        $this->setVariableRaw('sales', new Pap_Stats_Sales($this->statsParams));
        $this->setVariableRaw('actions', new Pap_Stats_Actions($this->statsParams));
        $this->setVariableRaw('transactions', new Pap_Stats_Transactions($this->statsParams));
        $this->setVariableRaw('transactionsTier', new Pap_Stats_TransactionsHigherTiers($this->statsParams));
    }    

    private function setUserVariables() {
        if ($this->user == null) {
            return;
        }

        $this->setUserCustomFields($this->user);
        $parentUser = $this->user->getParentUser();
        if ($parentUser != null) {
            $this->setUserCustomFields($parentUser, self::PARENT_PREFFIX);
        }

        $this->setVariable('userid', $this->user->getId());
    }

    /**
     * Get default currency symbol
     */
    private function getDefaultCurrencySymbol() {
        $defaultCurrency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
        return $defaultCurrency->getSymbol();
    }


    /**
     *
     * @return Pap_Common_User
     */
    public function getUser() {
        return $this->user;
    }

    public function setUser(Pap_Common_User $user) {
        $this->statsParams->setAffiliateId($user->getId());
        $this->user = $user;
    }

    public function loadUser($userId) {
        $this->user = new Pap_Common_User();
        $this->user->setId(Gpf_Session::getAuthUser()->getPapUserId());
        $this->user->load();
        return $this->user;
    }

    protected function addUserCustomFields() {
        $formFields = Pap_Common_UserFields::getInstance()->getUserFields(array('M', 'O', 'R'), Gpf::YES);

        foreach($formFields as $code => $name) {
            $this->addVariable($code, $this->_localize($name));
        }
        foreach($formFields as $code => $name) {
            $this->addVariable(self::PARENT_PREFFIX.$code, $this->_('Parent %s', $this->_localize($name)));
        }
        $this->insertIfNotExist(Pap_Db_Table_Users::PARENTUSERID, $this->_('Parent affiliate'));
        $this->insertIfNotExist(self::PARENT_PREFFIX . Pap_Db_Table_Users::PARENTUSERID, $this->_('Parent of parent affiliate'));
    }

    protected function insertIfNotExist($code, $name) {
        if (!$this->variableExist($code)) {
            $this->addVariable($code, $name);
        }
    }

    private function setUserCustomFields(Pap_Common_User $user, $preffix = '') {
        Pap_Common_UserFields::getInstance()->setUser($user);
        $formFields = Pap_Common_UserFields::getInstance()->getUserFieldsValues(Gpf::YES);

        foreach($formFields as $code => $value) {
            $this->setVariable($preffix.$code, $value);
        }

        $this->setVariable($preffix.Pap_Db_Table_Users::PARENTUSERID, Pap_Common_UserFields::getInstance()->getUserFieldValue(Pap_Db_Table_Users::PARENTUSERID));
    }
}
