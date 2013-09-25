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
abstract class Gpf_Common_CodeUtils_CodeBase extends Gpf_Object {

	/**
	 * @var array
	 */
	protected $replace;
	protected $format;

	/**
	 * @param String $format
	 */
	public function __construct($format) {
		$this->format = $format;
		preg_match_all("/(\{[zZ9X]+\})/", $this->format, $this->replace);
		if (count($this->replace[0]) == 0) {
			throw new Gpf_Exception('Unsupported format');
		}
	}

	/**
	 * @param String $char
	 * @return boolean
	 */
	protected function min($char) {
		return ($char == '9' ? 48 : ($char == 'z' ? 97 : 65));
	}

	/**
	 * @param String $char
	 * @return boolean
	 */
	protected function max($char) {
		return ($char == '9' ? 57 : ($char == 'z' ? 122 : 90));
	}
}
?>
