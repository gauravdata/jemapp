<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Tcpdf.class.php 19083 2008-07-10 16:32:14Z aharsani $
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
class Gpf_BarCode_Image {

    const CODE_TYPE_INTERLEAVED_2_OF_5 = 'I25';
    const CODE_TYPE_39 = 'C39';
    const CODE_TYPE_128A = 'C128A';
    const CODE_TYPE_128B = 'C128B';
    const CODE_TYPE_128C = 'C128C';

    /**
     * @var array
     */
    private static $barCodes;
    private $type;
    private $width;
    private $height;
    private $style;
    private $code;
    private $xres;
    private $font;

    public function __construct($barCodeType = 'I25') {
        $this->init();
        $this->type = $barCodeType;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setFontSize($fontSize) {
        $this->font = $fontSize;
    }

    public function setSize($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }

    public function setStyle($style) {
        $this->style = $style;
    }

    public function setXres($xres) {
        $this->xres = $xres;
    }

    public function generate() {
        $barCode = $this->createBarCode();
        $barCode->DrawObject($this->xres);
        $barCode->FlushObject();
        $barCode->DestroyObject();
    }

    private function init() {
        self::$barCodes = array();
        self::$barCodes[self::CODE_TYPE_INTERLEAVED_2_OF_5] = 'Gpf_BarCode_Type_I25';
        self::$barCodes[self::CODE_TYPE_39] = 'Gpf_BarCode_Type_C39';
        self::$barCodes[self::CODE_TYPE_128A] = 'Gpf_BarCode_Type_C128A';
        self::$barCodes[self::CODE_TYPE_128B] = 'Gpf_BarCode_Type_C128B';
        self::$barCodes[self::CODE_TYPE_128C] = 'Gpf_BarCode_Type_C128C';

        // All constants is defined in class Gpf_BarCode_Type_BarCode
        
        $this->style = 0 | 4 | 64 | 128;
        $this->width = 460;
        $this->height = 120;
        $this->font = 5;
        $this->xres = 2;
    }

    /**
     * @return Gpf_BarCode_Type_BarCode
     * @throws Gpf_Exception
     */
    private function createBarCode() {
        if (!preg_match('/^\d{2,10}$/', $this->code)) {
            throw new Gpf_BarCode_Exception('Code is not valid');
        }
        if (array_key_exists($this->type, self::$barCodes)) {
            return Gpf::newObj(self::$barCodes[$this->type], $this->width, $this->height, $this->style, $this->code);
        }
        throw new Gpf_BarCode_Exception('Bar code object not exist');
    }
}
?>
