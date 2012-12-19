<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DuplicateEntryException.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Templates_SmartySyntaxException extends Gpf_Exception  {

	const EXCEPTION_TYPE = 'Syntax error: ';
	
	/**
     * @param string $error_msg
     * @param string $tpl_file
     * @param integer $tpl_line
     * @param string $file
     * @param integer $line
     */
    function __construct($error_msg, $tpl_file = null, $tpl_line = null, $file = null, $line = null) {
        if(isset($file) && isset($line)) {
            $info = ' ('.basename($file).", line $line)";
        } else {
            $info = '';
        }
        if (isset($tpl_line) && isset($tpl_file)) {
        	parent::__construct(self::EXCEPTION_TYPE . '[in ' . $tpl_file . ' line ' . $tpl_line . "]: $error_msg$info");
            return;
        } 
        parent::__construct(self::EXCEPTION_TYPE . $error_msg . $info, $error_type);
    }

    protected function logException() {
    }
}
?>
