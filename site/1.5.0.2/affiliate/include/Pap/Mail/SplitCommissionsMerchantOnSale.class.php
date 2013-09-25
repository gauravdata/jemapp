<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Pap_Mail_SplitCommissionsMerchantOnSale extends Gpf_Mail_Template {

    private $saleId = null;

    public function __construct($saleId = null) {
        parent::__construct();
        $this->mailTemplateFile = 'splitcommissions_merchant_on_sale.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Split Commission Merchant - New Sale / Lead');
        $this->subject = Gpf_Lang::_runtime('New sale summary mail');
        $this->saleId = $saleId;
    }


    protected function initTemplateVariables() {
        $this->addVariable('transactions', $this->_('Transactions Info'));
        $this->addVariable('commission', $this->_('Commission value'));

        $this->addVariable('totalcost', $this->_('TotalCost'));
        $this->addVariable('orderid', $this->_('Order ID'));
        $this->addVariable('productid', $this->_('Product ID'));
    }

    protected function setVariableValues() {
        $transactions = $this->getTransactionsCollection();

        if ($transactions->getSize() == 0) {
            throw new Gpf_Exception('No transactions recorded, notification is not required');
        }

        $this->setVariable('transactions', $this->getTransactionsMailInfo($transactions));
        $this->setVariable('commission', $this->getTotalCommissionValue($transactions));

        $this->setVariable('totalcost', $this->getTotalCost($transactions));
        $this->setVariable('orderid', $this->getOrderId($transactions));
        $this->setVariable('productid', $this->getProductId($transactions));
    }

    protected function getProductId(Gpf_DbEngine_Row_Collection $transactions) {
        if ($transactions->getSize() == 0) {
            return $this->_('unknown');
        }
        return $transactions->get(0)->getProductId();
    }

    protected function getOrderId(Gpf_DbEngine_Row_Collection $transactions) {
        if ($transactions->getSize() == 0) {
            return $this->_('unknown');
        }
        return $transactions->get(0)->getOrderId();
    }

    protected function getTotalCost(Gpf_DbEngine_Row_Collection $transactions) {
        if ($transactions->getSize() == 0) {
            return $this->_('unknown');
        }
        return $transactions->get(0)->getTotalCostAsText();
    }

    protected function getTemplateFromFile() {
        $tmpl = new Gpf_Templates_Template(self::MAIL_TEMPLATE_DIR . $this->mailTemplateFile, 'install');
        return $tmpl->getTemplateSource();
    }

    private function getTotalCommissionValue(Gpf_DbEngine_Row_Collection $transactions) {
        $commissionValue = 0;
        foreach ($transactions as $transaction) {
            $commissionValue += $transaction->getCommission();
        }
        return $commissionValue;
    }

    protected function getTransactionsMailInfo(Gpf_DbEngine_Row_Collection $transactions) {
        $transactionsMailInfo = array();
        foreach ($transactions as $transaction) {
            $transactionsMailInfo[] = new Pap_Features_SplitCommissions_SplitCommissionsMailData($transaction);
        }
        return $transactionsMailInfo;
    }

    /**
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function getTransactionsCollection() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $selectBuilder->from->add(Pap_Db_Table_Transactions::getName());
        $selectBuilder->where->add(Pap_Db_Table_Transactions::SALE_ID,'=',$this->saleId);
        $recordSet = $selectBuilder->getAllRows();

        $transaction = new Pap_Common_Transaction();
        return $transaction->loadCollectionFromRecordset($recordSet);
    }
}
