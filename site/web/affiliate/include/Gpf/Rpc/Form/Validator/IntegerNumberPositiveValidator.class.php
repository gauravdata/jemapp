<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormHandler.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator extends Gpf_Rpc_Form_Validator_IntegerNumberValidator {
    
    /**
     * @return String
     */
    public function getText() {
        return $this->_('%s has to be positive integer', '%s');
    }
    
    /**
     * @param $value
     * @return boolean
     */
    public function validate($value) {
        return parent::validate($value) && $value >= 0;
    }
}
?>
