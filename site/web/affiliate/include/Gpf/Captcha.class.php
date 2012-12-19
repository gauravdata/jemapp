<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Captcha.class.php 18779 2008-06-24 14:21:27Z vzeman $
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
class Gpf_Captcha extends Gpf_Object {

    const TEXT_LENGTH = 5;
    const SESSION_PREFIX = 'captchas_';
    
    /**
     * @service
     * @anonym
     *
     * @param $name
     */
    public function getImage(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $lenght = self::TEXT_LENGTH;
        if (strlen($form->getFieldValue('length'))) {
            $lenght = $form->getFieldValue('length');
        }
        $text = $this->generateText($lenght);
        Gpf_Session::getInstance()->setVar(self::SESSION_PREFIX . $form->getFieldValue('name'), $text);

        $captchaGenerator = new Gpf_Common_Captcha_ImageGenerator($text);
        
        $height = 50;
        $width = self::TEXT_LENGTH * 30;
        
        if (strlen($params->get('height'))) {
            $height = $params->get('height');
        }
        if (strlen($params->get('width'))) {
            $height = $params->get('width');
        }
        
        $captchaGenerator->setSize($width, $height);
        return $captchaGenerator;
    }

    /**
     * @param String $captchaName - under this name is stored captcha code in session
     */
    private static function getCaptchaText($captchaName) {
        return Gpf_Session::getInstance()->getVar(self::SESSION_PREFIX . $captchaName);
    }

    private function generateText($length) {
        return strtolower(substr(md5(uniqid()), 0, $length));
    }

    /**
     * Check if captcha value is valid for given captcha widget
     *
     * @param string $captchaName
     * @param string $captchaText
     * @return boolean
     */
    public static function isValid($captchaName, $captchaText) {
        return self::getCaptchaText($captchaName) == strtolower($captchaText);
    }
}
