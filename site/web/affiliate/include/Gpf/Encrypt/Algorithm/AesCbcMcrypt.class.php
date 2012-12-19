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
class Gpf_Encrypt_Algorithm_AesCbcMcrypt extends Gpf_Object {

    private $key;
    private $iv;

    public function __construct($key, $iv) {
        if(!function_exists('mcrypt_module_open')){
            throw new Gpf_Exception('mcrypt extension not loaded');
        }

        $this->key  = $key;
        $this->iv = $iv;
        $this->cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    }

    public function encrypt($string) {
        mcrypt_generic_init($this->cipher, $this->key, $this->iv);
        $cipherText = mcrypt_generic($this->cipher, $string);
        mcrypt_generic_deinit($this->cipher);
        return $cipherText;
    }

}
?>
