<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Campaign.class.php 18128 2008-05-20 16:37:37Z mfric $
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
class Pap_Common_Utils_CurrencyUtils {

	const WHEREDISPLAY_LEFT = "1";
  	const WHEREDISPLAY_RIGHT = "2";
  
	protected $defaultCurrency = NULL;
	
	/**
	 * @var Pap_Common_Utils_CurrencyUtils
	 */
	private static $instance = NULL;

	/**
	 * @return Pap_Common_Utils_CurrencyUtils
	 */
	public static function getInstance() {
	    if (self::$instance == NULL) {
	        self::$instance = new self;
	    }
	    return self::$instance;
	}
	
	public static function create( Pap_Common_Utils_CurrencyUtils $currencyUtils) {
	    self::$instance = $currencyUtils;
	    return self::$instance;
	}
	
	/**
	 * Returns Gpf_Db_Currency object representing default currency
	 *
	 * @return Gpf_Db_Currency
	 */
	public static function getDefaultCurrency() {
		$instance = Pap_Common_Utils_CurrencyUtils::getInstance();
		if($instance->defaultCurrency == NULL) {
			$instance->defaultCurrency = $instance->loadDefaultCurrency();
		}
		return $instance->defaultCurrency;
	}

	/**
	 * formats the value to the standard currency format according to the default currency settings
	 * For example 2.0001 will be transformed to $ 2.00
	 *
	 * @param number $value
	 * @return string
	 */
	public static function toStandardCurrencyFormat($value, $commissionType = '') {
		if($commissionType == '%') {
			return $value. ' %';
		}
		if ($value == null || $value == '') {
			return $value;
		}

		$defaultCurrency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
		if($defaultCurrency == NULL) {
			return $value;
		}
		
		$symbol = $defaultCurrency->getSymbol();
		$precision = $defaultCurrency->getPrecision();
		$whereDisplay = $defaultCurrency->getWhereDisplay();
		
		return self::formatCurrency($value, $symbol, $whereDisplay, $precision);
	}
	
	/**
     * formats the value to the standard currency format according to the default currency settings
     * For example 2.0001 will be transformed to $ 2.0001. Does not round the number.
     *
     * @param string $value
     * @return string
     */
    public static function stringToCurrencyFormat($value) {
        if ($value == null || $value == '') {
            return $value;
        }

        $defaultCurrency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
        if($defaultCurrency == NULL) {
            return $value;
        }
        
        $currencySymbol = $defaultCurrency->getSymbol();
        $currencyWhereDisplay = $defaultCurrency->getWhereDisplay();
        
        if ($currencyWhereDisplay == Pap_Common_Utils_CurrencyUtils::WHEREDISPLAY_LEFT) {
            return $currencySymbol.' '.$value;
        }
        return $value.' '.$currencySymbol;
    }

	protected function loadDefaultCurrency() {
		$obj = new Gpf_Db_Currency();
		return $obj->getDefaultCurrency();
	}
	
	public static function formatCurrency($value, $symbol, $whereDisplay = Pap_Common_Utils_CurrencyUtils::WHEREDISPLAY_LEFT, $precision = 2) {
		$value = number_format(round($value, $precision), $precision );

		if($whereDisplay == Pap_Common_Utils_CurrencyUtils::WHEREDISPLAY_LEFT) {
			return $symbol.' '.$value;
		} else {
			return $value.' '.$symbol;
		}
	}
	
    
    public static function getDefaultCurrencyPrecision() {
        return self::getDefaultCurrency()->getPrecision();
    }
}
?>
