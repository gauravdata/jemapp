<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: NoCorrectImportFileException.class.php 19079 2008-07-10 13:40:20Z vzeman $
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
class Gpf_Csv_Parser {

	/**
	 * Parse csv string to array
	 *
	 * @param String $string
	 * @param String $delimiter
	 * @param String $enclosure
	 * @return array
	 */
	public static function parse($string = null, $delimiter = ';', $enclosure = '"') {
		$array = array();
		
		if ($string === null) {
			return $array;
		}
		
		while (true) {
			$column = self::getString($string, $delimiter, $enclosure);
			
			if ($column === false) {
				$array[] = $string;
				break;
			}
			$array[] = $column;
			if (strlen($string) <= (strlen($column) + 1)) {
				$array[] = '';
				break;
			}
			$string = substr($string, strlen($column) + 1);
		}
		
		$array = self::removeEnclosure($array, $enclosure);
			
		return $array;
	}

	private static function getString($string, $delimiter, $enclosure) {
		$inEnclosure = false;
			
		for ($i = 0; $i < strlen($string); $i++) {
			if ($string[$i] == $delimiter && !$inEnclosure) {
				return substr($string, 0, $i);
			} elseif ($string[$i] == $enclosure) {
				$inEnclosure = !$inEnclosure;
			}
		}
		
		return false;
	}

	private static function removeEnclosure(array $array, $enclosure) {
		$strings = array();

		foreach ($array as $value) {
			$value = trim($value);
			if (strpos($value, $enclosure) !== false) {
				if (substr($value, 0, 1) == $enclosure && substr($value, -1) == $enclosure) {
					$value = substr($value, 1, -1);
				}
				$value = str_replace($enclosure . $enclosure, $enclosure, $value);
			}

			$strings[] = $value;
		}

		return $strings;
	}
}
?>
