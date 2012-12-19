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
class Gpf_Rpc_Form_Validator_EmailValidator extends Gpf_Rpc_Form_Validator_RegExpValidator {

    public function __construct() {
        parent::__construct('/^(\w[\+-\._\w]*@[-\._\w]*\w\.\w{2,4})$/', $this->_('%s has to be valid email'));
    }

    public function validate($value) {
        if ($this->isEmpty($value)) {
            return false;
        }
        return parent::validate($value);
    }
}
?>
