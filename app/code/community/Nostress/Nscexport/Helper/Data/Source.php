<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Helper.
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Helper_Data_Source extends Nostress_Nscexport_Helper_Data
{	
	const DEF_CATEGORY_LOWEST_LEVEL = 2;
	
	protected function toOptionArray($input)
	{
		foreach ($input as $key => $data) {
			$return[] = array('label' => $data, 'value' => $key);
		}
		return $return;
	}
	
	public function getDelimitersOptionArray() {
		$return = array();
		$delimiters = array("." => $this->__("Dot")." - ( . )",
							"," => $this->__("Comma")." - ( , )",
							"" => $this->__("Empty")." - price in cents",);
		natsort($delimiters);
		return $this->toOptionArray($delimiters);
	}
	
	public function getEnclosureOptionArray() {
		$return = array();
		$enclosures = array('"' => $this->__("Double quotes").' - ( " )',
							"'" => $this->__("Quotes")." - ( ' )",
							"" => $this->__("Empty - no enclosure"));
		return $this->toOptionArray($enclosures);
	}
	
	public function getColumnDelimiterOptionArray() {
		$return = array();
		$delimiters = array("|" => $this->__("Pipe")." - ( | )",
							"," => $this->__("Comma")." - ( , )",
							"\t" => $this->__("Tab")." - ( \\t )",
							" " => $this->__("Space")." - ( ' ' )",
							";" => $this->__("Semicolon")." - ( ; )",
							"/" => $this->__("Slash")." - ( / )",
							"-" => $this->__("Dash")." - ( - )",
							"*" => $this->__("Star")." - ( * )",
							'\\' => $this->__("Backslash")." - ( \\ )",
							":" => $this->__("Colon")." - ( : )",
							"#" => $this->__("Grid")." - ( # )",
							"&" => $this->__("Ampersand")." - ( & )",
							"~" => $this->__("Tilde")." - ( ~ )"
							);
		
		return $this->toOptionArray($delimiters);
	}
	
	public function getNewlineDelimiterOptionArray() {
		$return = array();
		$delimiters = array("\r\n" => "CR+LF - ( \\r\\n )",
							"\n\r" => "LF+CR - ( \\n\\r )",
							"\n" => "LF - ( \\n )",
							"\r" => "CR - ( \\r )"
							);
		
		return $this->toOptionArray($delimiters);
	}
	
	public function getCategoryLevelOptionArray()
	{
		$return = array();
		$delimiters = array("1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
		
		return $this->toOptionArray($delimiters);
	}
}