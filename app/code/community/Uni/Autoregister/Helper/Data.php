<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Autoregister
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Autoregister_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MIN_PASSWORD_LENGHT = 6;
    public function isAutoRegistrationEnabled(){
        return Mage::getStoreConfig('autoregister/autoregister/enabled');
    }

    private function getPasswordLength(){
        $minLength = self::MIN_PASSWORD_LENGHT;
        $length = (int)Mage::getStoreConfig('autoregister/autoregister/password_length');
        return (($length>=$minLength)?$length:$minLength);
    }
    
    private function getPasswordChar(){        
        $chars = Mage::getStoreConfig('autoregister/autoregister/password_char');
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        return $chars;
    }

    public function getRandomPassword(){
        $len = $this->getPasswordLength();
        $chars = $this->getPasswordChar();        
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
}
