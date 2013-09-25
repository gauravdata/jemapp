<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ChannelsForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Gpf_Common_CodeUtils_CodeValidator extends Gpf_Common_CodeUtils_CodeBase {

	/**
	 * @param String $code
	 * @return boolean
	 */
	public function validate($code) {
		if (!$this->hasFormatSize($code)) {
			return false;
		}
		return $this->validateCode($code);
	}

	private function hasFormatSize($code) {
		$bracketsCount = substr_count($this->format, '{') + substr_count($this->format, '}');
		if (strlen($code) == strlen($this->format) - $bracketsCount) {
			return true;
		}
		return false;
	}

	private function validateCode($code) {
		$inBrackets = false;
		$codePosition = 0;
		for ($i = 0; $i < strlen($this->format); $i++) {
			$formatChar = substr($this->format, $i, 1);
			if ($formatChar == '{') {
				$inBrackets = true;
				continue;
			}
			if ($formatChar == '}') {
				$inBrackets = false;
				continue;
			}
			$codeChar = substr($code, $codePosition, 1);
			if ($inBrackets && !$this->isInCharFormat($codeChar, $formatChar)) {
				return false;
			}
			if (!$inBrackets && $codeChar != $formatChar) {
				return false;
			}
			$codePosition++;
		}
		return true;
	}

	private function isInCharFormat($codeChar, $formatChar) {
		if ($formatChar == 'X') {
			return $this->equalToFormatChar($codeChar, '9') ||
			$this->equalToFormatChar($codeChar, 'z') ||
			$this->equalToFormatChar($codeChar, 'Z');
		}
		return $this->equalToFormatChar($codeChar, $formatChar);
	}

	private function equalToFormatChar($codeChar, $formatChar) {
		return ord($codeChar) >= $this->min($formatChar) && ord($codeChar) <= $this->max($formatChar);
	}
}
?>
