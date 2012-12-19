<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RgbColor.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Common_Captcha_RgbColor extends Gpf_Object {
    public $r;
    public $g;
    public $b;

    public function __construct($r, $g, $b) {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }
    
    public function add($r, $g, $b) {
        $this->r += $r;
        $this->g += $g;
        $this->b += $b;
    }
    
    /**
     * @return Gpf_Common_Captcha_RgbColor
     */
    public static function createRandomColor($min, $max) {
        srand((double)microtime() * 1000000);
        $r = intval(rand($min, $max));
        srand((double)microtime() * 1000000);
        $g = intval(rand($min, $max));
        srand((double)microtime() * 1000000);
        $b = intval(rand($min, $max));
        return new Gpf_Common_Captcha_RgbColor($r, $g, $b);
    }
}
