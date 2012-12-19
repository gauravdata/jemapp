<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: User.class.php 18993 2008-07-07 08:20:50Z mjancovic $
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
class Pap_Common_TransactionFields extends Gpf_Object  {

    const TRANSACTIONID = 'transactionid';
    const COMMISSION = 'commission';
    const TOTALCOST = 'totalcost';
    const ORDERID = 'orderid';
    const PRODUCTID = 'productid';
    const TIER = 'tier';
    const CAMPAIGNID = 'campaignid';
    const CAMPAIGNNAME = 'campaignname';
    const STATUS = 'status';
    const STATUSCODE = 'statuscode';
    const TYPE = 'type';
    const RAWTYPE = 'rawtype';
    const ACTIONNAME = 'actionName';
    const REFERERURL = 'refererurl';
    const IP = 'ip';
    const COUNTRYCODE = 'countrycode';
    const FIRSTCLICKTIME = 'firstclicktime';
    const FIRSTCLICKREFERER = 'firstclickreferer';
    const FIRSTCLICKIP = 'firstclickip';
    const FIRSTCLICKDATA1 = 'firstclickdata1';
    const FIRSTCLICKDATA2 = 'firstclickdata2';
    const LASTCLICKTIME = 'lastclicktime';
    const LASTCLICKREFERER = 'lastclickreferer';
    const LASTCLICKIP = 'lastclickip';
    const LASTCLICKDATA1 = 'lastclickdata1';
    const LASTCLICKDATA2 = 'lastclickdata2';
    const SALEDATA1 = 'saledata1';
    const SALEDATA2 = 'saledata2';
    const SALEDATA3 = 'saledata3';
    const SALEDATA4 = 'saledata4';
    const SALEDATA5 = 'saledata5';
    const MERCHANTNOTE = 'merchantnote';
    const SYSTEMNOTE = 'systemnote';
    const ORIGINALCURRENCY = 'originalcurrency';
    const ORIGINALCURRENCYVALUE = 'originalcurrencyvalue';
    const ORIGINALCURRENCYRATE = 'originalcurrencyrate';

    /**
     * @var Pap_Common_Transaction
     */
    private $transaction;
    /**
     * @var instance
     */
    static protected $instance = null;
    static private $transactionFields = null;

    /*
     * TODO: this cache needs to be refactored
     */
    private $cache = array();

    private function __construct() {
        $this->transaction = null;
    }

    /**
     * returns instance of TransactionFields class
     *
     * @return Pap_Common_TransactionFields
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Pap_Common_TransactionFields();
        }
        return self::$instance;
    }

    /**
     * Loads list of transaction fields
     *
     * @anonym
     * @service
     */
    public function getFields() {
        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array('code', 'name'));
        $fields = $this->getTransactionFields();
        foreach($fields as $code => $name) {
            $result->add(array($code, $name));
        }
        return $result;
    }

    /**
     * returns array of transaction fields
     *
     * @return unknown
     */
    public function getTransactionFields() {
        if (is_array(self::$transactionFields)) {
            return self::$transactionFields;
        }

        self::$transactionFields = array();
        $this->addField(self::TRANSACTIONID, $this->_('Transaction ID'));
        $this->addField(self::COMMISSION, $this->_('Commission'));
        $this->addField(self::TOTALCOST, $this->_('TotalCost'));
        $this->addField(self::ORDERID, $this->_('Order ID'));
        $this->addField(self::PRODUCTID, $this->_('Product ID'));
        $this->addField(self::TIER, $this->_('Tier'));
        $this->addField(self::CAMPAIGNID, $this->_('Campaign ID'));
        $this->addField(self::CAMPAIGNNAME, $this->_('Campaign name'));
        $this->addField(self::STATUS, $this->_('Status'));
        $this->addField(self::STATUSCODE, $this->_('Status'));
        $this->addField(self::TYPE, $this->_('Type'));
        $this->addField(self::RAWTYPE, $this->_('RawType'));
        $this->addField(self::ACTIONNAME, $this->_('Action name'));

        $this->addField(self::REFERERURL, $this->_('Referer URL'));
        $this->addField(self::IP, $this->_('IP'));
        $this->addField(self::COUNTRYCODE, $this->_('Country code'));
        $this->addField(self::FIRSTCLICKTIME, $this->_('First click - time'));
        $this->addField(self::FIRSTCLICKREFERER, $this->_('First click - referer'));
        $this->addField(self::FIRSTCLICKIP, $this->_('First click - IP'));
        $this->addField(self::FIRSTCLICKDATA1, $this->_('First click - data1'));
        $this->addField(self::FIRSTCLICKDATA2, $this->_('First click - data2'));
        $this->addField(self::LASTCLICKTIME, $this->_('Last click - time'));
        $this->addField(self::LASTCLICKREFERER, $this->_('Last click - referer'));
        $this->addField(self::LASTCLICKIP, $this->_('Last click - IP'));
        $this->addField(self::LASTCLICKDATA1, $this->_('Last click - data1'));
        $this->addField(self::LASTCLICKDATA2, $this->_('Last click - data2'));
        $this->addField(self::SALEDATA1, $this->_('Transaction data1'));
        $this->addField(self::SALEDATA2, $this->_('Transaction data2'));
        $this->addField(self::SALEDATA3, $this->_('Transaction data3'));
        $this->addField(self::SALEDATA4, $this->_('Transaction data4'));
        $this->addField(self::SALEDATA5, $this->_('Transaction data5'));
        $this->addField(self::MERCHANTNOTE, $this->_('Note to merchant'));
        $this->addField(self::SYSTEMNOTE, $this->_('Note to affiliate'));
        $this->addField(self::ORIGINALCURRENCY, $this->_('Original currency'));
        $this->addField(self::ORIGINALCURRENCYVALUE, $this->_('Original currency value'));
        $this->addField(self::ORIGINALCURRENCYRATE, $this->_('Original currency rate'));

        return self::$transactionFields;
    }

    private function addField($code, $title) {
        self::$transactionFields[$code] = $title;
    }
    
    public function removeFromCache($transactionId){
        unset($this->cache[$transactionId]);
    }

    public function getTransactionFieldsValues() {
        if($this->transaction == null) {
            throw new Gpf_Exception("You have to set Transaction before getting transaction fields value!");
        }

        if (array_key_exists($this->transaction->getId(), $this->cache)) {
            return $this->cache[$this->transaction->getId()];
        }

        $fields = $this->getTransactionFields();
        $result = array();
        foreach($fields as $code => $name) {
            $result[$code] = $this->getTransactionFieldValue($code);
        }
        $this->cache[$this->transaction->getId()] = $result;
        return $result;
    }

    public function getTransactionFieldValue($code) {
        if($this->transaction == null) {
            throw new Gpf_Exception("You have to set Transaction before getting transaction fields value!");
        }

        if($code == self::TRANSACTIONID) {
            return $this->transaction->get(Pap_Db_Table_Transactions::TRANSACTION_ID);
        } else if($code == self::COMMISSION) {
            return $this->transaction->getCommissionAsText();
        } else if($code == self::TOTALCOST) {
            return $this->transaction->getTotalCostAsText();
        } else if($code == self::ORDERID) {
            return $this->transaction->getOrderId();
        } else if($code == self::PRODUCTID) {
            return $this->transaction->getProductId();
        } else if($code == self::TIER) {
            return $this->transaction->getTier();
        } else if($code == self::CAMPAIGNID) {
            return $this->transaction->getCampaignId();
        } else if($code == self::CAMPAIGNNAME) {
            return $this->getCampaignName($this->transaction->getCampaignId());
        } else if($code == self::STATUS) {
            return $this->getStatus($this->transaction->getStatus());
        } else if($code == self::STATUSCODE) {
            return $this->transaction->getStatus();
        } else if($code == self::TYPE) {
            return $this->getType($this->transaction->getType());
        } else if($code == self::RAWTYPE) {
            return $this->transaction->getType();
        } else if($code == self::ACTIONNAME) {
            return $this->getActionName($this->transaction->getType(), $this->transaction->getCommissionTypeId());
        } else if($code == self::SALEDATA1) {
            return $this->transaction->getData1();
        } else if($code == self::SALEDATA2) {
            return $this->transaction->getData2();
        } else if($code == self::SALEDATA3) {
            return $this->transaction->getData3();
        } else if($code == self::SALEDATA4) {
            return $this->transaction->getData4();
        } else if($code == self::SALEDATA5) {
            return $this->transaction->getData5();
        } else if($code == self::ORIGINALCURRENCY) {
            return $this->getOriginalCurrencyName($this->transaction->get(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID));
        }
        try {
            return $this->transaction->get($code);
        } catch (Gpf_Exception $e) {
        }
        return '';
    }

    public function setTransaction(Pap_Common_Transaction $transaction) {
        $this->transaction = $transaction;
    }

    /**
     * replaces transaction fields values in standard format (${#data1#)
     * with their values
     *
     * @param string $text
     */
    public function replaceTransactionConstantsInText($text) {
        $values = $this->getTransactionFieldsValues();

        // simple replace
        foreach($values as $code => $value) {
            $text = Pap_Common_UserFields::replaceCustomConstantInText($code, $value, $text);
        }

        return $text;
    }

    /**
     * removes transaction fields values in standard format (${#data1#)
     *
     * @param string $text
     */
    public function removeTransactionConstantsInText($text) {
        $fields = $this->getTransactionFields();
        foreach($fields as $code => $value) {
            $text = Pap_Common_UserFields::replaceCustomConstantInText($code, '', $text);
        }

        return $text;
    }

    /**
     * remove user constants in standard format {*some comment*}
     * @throws Gpf_Exception
     *
     * @param string $text
     */
    public function removeTransactionCommentsInText($text) {
        return Pap_Common_UserFields::removeCommentsInText($text);
    }

    protected function getCampaignName($campaignId) {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
            return $campaign->getName();
        } catch (Gpf_Exception $e) {
            return '';
        }
    }

    protected function getStatus($status) {
        $constants = new Pap_Common_Constants();
        return $constants->getStatusAsText($status);
    }

    protected function getType($type) {
        return Pap_Common_Constants::getTypeAsText($type);
    }

    protected function getActionName($type, $id) {
        if ($type != Pap_Common_Constants::TYPE_ACTION) {
            return $this->_('Sale');
        } else {
            $commType = new Pap_Db_CommissionType();
            $commType->setId($id);
            $commType->load();
            return $commType->getName();
        }
    }

    protected function getOriginalCurrencyName($currencyId) {
        if($currencyId == null || $currencyId == '') {
            return '';
        }

        $obj = new Gpf_Db_Currency();
        try {
            $obj->setId($currencyId);
            $obj->load();
            return $obj->getName();
        } catch (Gpf_Exception $e) {
        }
        return '';
    }
}

?>
