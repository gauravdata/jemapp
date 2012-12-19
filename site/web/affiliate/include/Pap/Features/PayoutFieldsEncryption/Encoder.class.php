<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene Dohan
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Features_PayoutFieldsEncryption_Encoder extends Gpf_Encrypt_Algorithm_AesCbc {

    private $noEncoding = false;

    public function __construct($key = '', $iv = '') {
        if ($key == '' && $iv == '') {
            $this->noEncoding = true;
            return;
        }
        parent::__construct($key, $iv);
    }
    
    public function encrypt($x) {
        if ($this->noEncoding === true) {
            return $x;
        }
        return base64_encode(parent::encrypt($x));
    }

    public function decrypt($y) {
        if ($this->noEncoding === true) {
            return $y;
        }
        return parent::decrypt(base64_decode($y));
    }
}
?>
