<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package GwtPhpFramework
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: Currency.class.php 25168 2009-08-11 12:59:01Z mgalik $
*
*   Licensed under the Quality Unit, s.r.o. Dual License Agreement, 
*   Version 1.0 (the "License"); you may not use this file except in compliance 
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/gpf
* 
*/

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_Currency extends Gpf_DbEngine_Row {
    const DEFAULT_CURRENCY_VALUE = "1";
    const ACCOUNT_ID = "accountid";
    
    const DISPLAY_LEFT = 1;
    const DISPLAY_RIGHT = 2;
    
    /* is default constants */
    const ISDEFAULT_NO = '0';    
    
    function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Gpf_Db_Table_Currencies::getInstance());
        parent::init();
    }  

    /**
     * returns currency found by name or exception
     *
     * @service currency read
     * @param $ids
     * @return Gpf_Db_Currency
     */    
    public function findCurrencyByCode($currencyCode) {
		$result = new Gpf_Data_RecordSet();

		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::ID, 'currencyid');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::NAME, 'name');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, 'symbol');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::PRECISION, 'cprecision');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::IS_DEFAULT, 'isdefault');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, 'wheredisplay');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::EXCHANGERATE, 'exchrate');
		$selectBuilder->from->add(Gpf_Db_Table_Currencies::getName());
		$selectBuilder->where->add('name', '=', $currencyCode);
		$selectBuilder->limit->set(0, 1);
		$result->load($selectBuilder);

		if($result->getSize() == 0) {
			throw new Gpf_DbEngine_NoRowException($selectBuilder);
		}

		foreach($result as $record) {
			$currency = new Gpf_Db_Currency();
			$currency->fillFromRecord($record);
			return $currency;
		}    	
		
		throw new Gpf_DbEngine_NoRowException($selectBuilder);
	}    
	
	/**
	 * returns default currency or an exception
	 *
	 * @return Gpf_Db_Currency
	 */
	public static function getDefaultCurrency() {
		$result = new Gpf_Data_RecordSet();

		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::ID, 'currencyid');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::NAME, 'name');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, 'symbol');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::PRECISION, 'cprecision');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::IS_DEFAULT, 'isdefault');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, 'wheredisplay');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::EXCHANGERATE, 'exchrate');
		$selectBuilder->from->add(Gpf_Db_Table_Currencies::getName());
		$selectBuilder->where->add('isdefault', '=', 1);
		$selectBuilder->limit->set(0, 1);
		$result->load($selectBuilder);

		if($result->getSize() == 0) {
			throw new Gpf_DbEngine_NoRowException($selectBuilder);
		}

		foreach($result as $record) {
			$currency = new Gpf_Db_Currency();
			$currency->fillFromRecord($record);
			return $currency;
		}    	
		
		throw new Gpf_DbEngine_NoRowException($selectBuilder);  		
	}
	
	public function getIsDefault() {
    	return $this->get(Gpf_Db_Table_Currencies::IS_DEFAULT);
    }    
    
	public function getName() {
    	return $this->get(Gpf_Db_Table_Currencies::NAME);
    }    

	public function getSymbol() {
    	return $this->get(Gpf_Db_Table_Currencies::SYMBOL);
    }    

	public function getWhereDisplay() {
    	return $this->get(Gpf_Db_Table_Currencies::WHEREDISPLAY);
    }    
    
    public function getId() {
    	return $this->get(Gpf_Db_Table_Currencies::ID);
    }    
    
    public function getExchangeRate() {
    	return $this->get(Gpf_Db_Table_Currencies::EXCHANGERATE);
    }    

    public function getPrecision() {
    	return $this->get(Gpf_Db_Table_Currencies::PRECISION);
    }

    public function setIsDefault($isDefault) {
        return $this->set(Gpf_Db_Table_Currencies::IS_DEFAULT, $isDefault);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Currencies::ACCOUNTID, $accountId);
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_Currencies::ID, $id);
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_Currencies::NAME, $name);
    }    

    public function setSymbol($symbol) {
        $this->set(Gpf_Db_Table_Currencies::SYMBOL, $symbol);
    }    

    public function setWhereDisplay($whereDisplay) {
        $this->set(Gpf_Db_Table_Currencies::WHEREDISPLAY, $whereDisplay);
    }    
    
    public function setPrecision($precision) {
        $this->set(Gpf_Db_Table_Currencies::PRECISION, $precision);
    }
    
    public function setExchangeRate($exchangeRate) {
        $this->set(Gpf_Db_Table_Currencies::EXCHANGERATE, $exchangeRate);
    }  

    /**
     * Gets currency names for CurrencySearchListBox
     *
     * @service currency read
     * @param $search
     */
    public function getCurrencies(Gpf_Rpc_Params $params) {
        $searchString = $params->get('search');
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_Currencies::ID, "id");
        $select->select->add(Gpf_Db_Table_Currencies::NAME, "name");
        $select->from->add(Gpf_Db_Table_Currencies::getName());
        $select->where->add(Gpf_Db_Table_Currencies::NAME, "LIKE", "%".$searchString."%");

        $result = new Gpf_Data_RecordSet();
        $result->load($select);
        
        return $result;
    }
}

?>
