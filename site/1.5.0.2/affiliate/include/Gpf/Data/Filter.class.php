<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Gpf_Data_Filter extends Gpf_Object implements Gpf_Rpc_Serializable {
    const LIKE = "L";
    const NOT_LIKE = "NL";
    const EQUALS = "E";
    const NOT_EQUALS = "NE";
    
    const DATE_EQUALS = "D=";
    const DATE_GREATER = "D>";
    const DATE_LOWER = "D<";
    const DATE_EQUALS_GREATER = "D>=";
    const DATE_EQUALS_LOWER = "D<=";
    const DATERANGE_IS = "DP";
    const TIME_EQUALS = "T=";
    const TIME_GREATER = "T>";
    const TIME_LOWER = "T<";
    const TIME_EQUALS_GREATER = "T>=";
    const TIME_EQUALS_LOWER = "T<=";
    
    const RANGE_TODAY = 'T';
    const RANGE_YESTERDAY = 'Y';
    const RANGE_LAST_7_DAYS = 'L7D';
    const RANGE_LAST_30_DAYS = 'L30D';
    const RANGE_LAST_90_DAYS = 'L90D';
    const RANGE_THIS_WEEK = 'TW';
    const RANGE_LAST_WEEK = 'LW';
    const RANGE_LAST_2WEEKS = 'L2W';
    const RANGE_LAST_WORKING_WEEK = 'LWW';
    const RANGE_THIS_MONTH = 'TM';
    const RANGE_LAST_MONTH = 'LM';
    const RANGE_THIS_YEAR = 'TY';
    const RANGE_LAST_YEAR = 'LY';
                
	private $code;
	private $operator;
	private $value;
	
	public function __construct($code, $operator, $value) {
		$this->code = $code;
		$this->operator = $operator;
		$this->value = $value;
	}
	
	public function toObject() {
		return array($this->code, $this->operator, $this->value);
	}
	
	public function toText() {
		throw new Gpf_Exception("Unsupported");
	}
}

?>
