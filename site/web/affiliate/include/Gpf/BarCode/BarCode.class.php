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
class Gpf_BarCode_BarCode extends Gpf_Object {

    const IMAGE_CODE = 'IC';

    /**
     * Output bar code image to the browser
     *
     * @param $code   
     */
    public function getImage($imageCode) {
        if (!$this->isValid($imageCode)) {
            throw new Gpf_BarCode_Exception('Image code is not valid');
        }
        $barCodeImage = new Gpf_BarCode_Image();
        $barCodeImage->setSize(180, 120);
        $barCodeImage->setCode($imageCode);
        $barCodeImage->generate();        
    }
    
    /** 
     * @param $imageCode
     * @return String
     */   
    public function getLink($imageCode) {
        $link = Gpf_Paths::getInstance()->getFullScriptsUrl() . 'barcode.php';
        $link .= '?' . self::IMAGE_CODE . '=' . $imageCode;
        
        return '<img src="' . $link . '">';        
    }
    
    protected function isValid($imageCode) {        
        $validator = new Gpf_Rpc_Form_Validator_RegExpValidator('/^\d{8}$/', '');
        return $validator->validate($imageCode);
    } 
}
?>
