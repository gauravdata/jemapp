<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: Account.class.php 21660 2008-10-16 13:14:12Z mbebjak $
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
class Pap_Alert_Xml extends Gpf_Object {

	private $buffer;
	private $error = false;

	function __construct() {
		$this->buffer = '';
	}

	public function write($type, array $data) {
		if ($type != null && $data != null && !$this->error) {
			$this->buffer .= "<notification type=\"$type\"";
			foreach($data as $name => $value) {
				$this->buffer .= " $name=\"".$this->correctValue($value)."\"";
			}

			$this->buffer .= "/>\n";
		}
	}

	private function correctValue($str) {
		$str = str_replace("'", "", $str);
		$str = str_replace("&", "&amp;", $str);
		$str = str_replace("\"", "", $str);
		$str = str_replace("<", "&lt;", $str);
		$str = str_replace(">", "&gt;", $str);

		return $str;
	}
	
	public function writeError($error) {
		$this->buffer = "<error>$error</error>\n";
	}

	public function toString() {
		echo "<xml>\n".$this->buffer."</xml>";
	}
}
?>
