<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
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
class Gpf_Common_NumberUtils {

	/**
	 * @var Gpf_Common_NumberUtils
	 */
	private static $instance = NULL;
	
	/**
	 * @return Gpf_Common_NumberUtils
	 */
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	private function __construct() {
	}

	/**
	 * formats the value to the number format according to the number settings
	 *
	 * @param number $value
	 * @param int $precision
	 * @return string
	 */
	public static function toStandardNumberFormat($value, $precision = 2) {
		if ($value == null || $value == '') {
			return $value;
		}

		$thousandsSeparator = Gpf_Settings_Regional::getInstance()->getThousandsSeparator();
		$decimalSeparator = Gpf_Settings_Regional::getInstance()->getDecimalSeparator();
		if($thousandsSeparator == NULL || $decimalSeparator == NULL) {
			return $value;
		}

		return self::formatNumber($value, $decimalSeparator, $thousandsSeparator, $precision);
	}


	public static function formatNumber($value, $decimalSeparator, $thousandsSeparator, $precision = 2) {
		return number_format(round($value, $precision), $precision, $decimalSeparator,  $thousandsSeparator);
	}
}
?>
