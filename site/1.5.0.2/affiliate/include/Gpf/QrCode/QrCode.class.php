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
class Gpf_QrCode_QrCode extends Gpf_Object {
    
    const QR_IMAGE_CODE = 'QR';
    const GENERATE_URL = 'http://api.qrserver.com/v1/create-qr-code/';
    
    public static function getQrImageUrl($text) {
        return self::GENERATE_URL . '?size=200x200&bgcolor=FFFFFF&data=' . urlencode($text);
    }
    /**
     * Output bar code image to the browser
     *
     * @param $code   
     */
    public function getImage($imageCode) {
        header("Content-Type: image/png");
        $qrimage = imagecreatefrompng(Gpf_QrCode_QrCode::getQrImageUrl($imageCode));
        imagepng($qrimage);   
    }
    
    /** 
     * @param $imageCode
     * @return String
     */   
    public function getLink($imageCode) {
        $link = Gpf_Paths::getInstance()->getFullScriptsUrl() . 'qrcode.php';
        $link .= '?' . self::QR_IMAGE_CODE . '=' . $imageCode;
        
        return '<img src="' . $link . '">';        
    }
}
?>
