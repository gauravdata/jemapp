<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MissingFieldException.class.php 20528 2008-09-03 11:58:11Z mbebjak $
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
class Gpf_DbEngine_Row_MissingFieldException extends Gpf_Exception {
    public function __construct($fieldCode, $class) {
        parent::__construct("Invalid field (column) ".$fieldCode." in class ".$class);
    }
    
    protected function logException() {
    }
}

?>
