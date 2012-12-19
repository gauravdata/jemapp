<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author  Rafael M. Salvioni , Rene Dohan
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
class Gpf_Encrypt_Algorithm_RC4 extends Gpf_Object {
    //    /**
    //     * Store the permutation vectors
    //     *
    //     * @var array
    //     */
    //    private $S = array();
    //
    //    /**
    //     * Swaps values on the permutation vector.
    //     *
    //     * @param int $v1 Value 1
    //     * @param int $v2 Value 2
    //     */
    //    private function swap(&$v1, &$v2){
    //        $v1 = $v1 ^ $v2;
    //        $v2 = $v1 ^ $v2;
    //        $v1 = $v1 ^ $v2;
    //    }
    //
    //    /**
    //     * Make, store and returns the permutation vector about the key.
    //     *
    //     * @param string $key Key
    //     * @return array
    //     */
    //    private function KSA($key){
    //        $idx = crc32($key);
    //        if (!isset($this->S[$idx])) {
    //            $S   = range(0, 255);
    //            $j   = 0;
    //            $n   = strlen($key);
    //            for ($i = 0; $i < 255; $i++) {
    //                $char  = ord($key{$i % $n});
    //                $j     = ($j + $S[$i] + $char) % 256;
    //                $this->swap($S[$i], $S[$j]);
    //            }
    //            $this->S[$idx] = $S;
    //        }
    //        return $this->S[$idx];
    //    }
    //
    //    /**
    //     * Encrypt the data.
    //     *
    //     * @param string $key Key
    //     * @param string $data Data string
    //     * @return string
    //     */
    //    public function encrypt($key, $data){
    //        $S    = $this->KSA($key);
    //        $n    = strlen($data);
    //        $i    = $j = 0;
    //        for ($m = 0; $m < $n; $m++) {
    //            $i        = ($i + 1) % 256;
    //            $j        = ($j + $S[$i]) % 256;
    //            $this->swap($S[$i], $S[$j]);
    //            $char     = ord($data{$m});
    //            $char     = $S[($S[$i] + $S[$j]) % 256] ^ $char;
    //            $data[$m] = chr($char);
    //        }
    //        return $data;
    //    }

    /**
     * Encrypt given plain text using the key with RC4 algorithm.
     * All parameters and return value are in binary format.
     *
     * @param string key - secret key for encryption
     * @param string pt - plain text to be encrypted
     * @return string
     */
    function encrypt($key, $pt) {
        $s = array();
        for ($i=0; $i<256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        $x;
        for ($i=0; $i<256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $ct = '';
        $y;
        for ($y=0; $y<strlen($pt); $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $ct .= $pt[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        return $ct;
    }


    /**
     * Decrypts the data.
     *
     * @param string $key Key
     * @param string $data Encripted data
     * @return string
     */
    public function decrypt($key, $data){
        return $this->encrypt($key, $data);
    }
}
