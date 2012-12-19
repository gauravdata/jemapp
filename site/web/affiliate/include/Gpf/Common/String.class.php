<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 * 	 @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: String.class.php 26485 2009-12-09 11:50:13Z vzeman $
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
class Gpf_Common_String extends Gpf_Object {

    const NUMERIC_CHARS = '1234567890';
    const SPECIAL_CHARS = '!@#$%^&*|';
    const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwzxy';
    const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function generateId($length = 8) {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    public static function generatePassword($length) {

        //check min length
        if ($length < Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH)) {
            $length = Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH);
        }
        //check max length
        if ($length > Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH)) {
            $length = Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH);
        }

        $chars = str_shuffle(self::NUMERIC_CHARS . self::LOWERCASE_CHARS .
                 self::UPPERCASE_CHARS);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0,strlen($chars)-1)];
        }

        $result = self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_SPECIAL, self::SPECIAL_CHARS, $result);
        $result = self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_DIGITS, self::NUMERIC_CHARS, $result);
        return self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_LETTERS, self::UPPERCASE_CHARS . self::LOWERCASE_CHARS, $result);
    }

    /**
     * Add minimum one character into password from string includeCharacters
     * if it doesn't contain already such character
     *
     * @param $settingName
     * @param $includeCharacters
     * @param $password
     * @return unknown_type
     */
    private static function normalizePassword($settingName, $includeCharacters, $password) {
        if (Gpf_Settings::get($settingName) == Gpf::YES &&
            !preg_match('/[' . preg_quote($includeCharacters) . ']/', $password)) {
            $password[rand(0,strlen($password)-1)] = $includeCharacters[rand(0, strlen($includeCharacters)-1)];
        }
        return $password;
    }

    /**
     * Convert text on input to be suitable in any URL
     *
     * @param string $text
     * @return string
     */
    public static function textToUrl($text) {
        return preg_replace('/[^a-zA-Z0-9\-]/', '',str_replace(' ', '-', $text));
    }

    /**
     * Convert simple text to html
     *
     * @param string $text Simple text
     * @return string input string in html format
     */
    public function text2Html($text) {
        return str_replace("\n", '<br>', htmlspecialchars($text));
    }
}

?>
