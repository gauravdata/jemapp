<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
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
class Gpf_Lang {
    
    /**
     * Translate input message into selected language.
     * If translation will not be found, return same message.
     *
     * @param string $message
     * @return string
     */
    public static function _($message, $args = null, $langCode = '') {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        $dictionary = Gpf_Lang_Dictionary::getInstance($langCode);
        return self::_replaceArgs($dictionary->get($message), $args);
    }
    
    /**
     * Replace arguments in message.
     *
     * @param string $message
     * @param $args
     * @return string
     */
    public static function _replaceArgs($message, $args = null) {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        if(count($args) > 1 && substr_count($message, '%s') < count($args)) {
            array_shift($args);
            return vsprintf($message, $args);
        }
        return $message;
    }
    
    /**
     * Translate input message into default language defined in language settings for account.
     * This function should be used in case message should be translated to default language (e.g. log messages written to event log)
     *
     * @param string $message
     * @return string
     */
    public static function _sys($message, $args = null) {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        $dictionary = Gpf_Lang_Dictionary::getInstance(Gpf_Lang_Dictionary::getDefaultSystemLanguage());
        return self::_replaceArgs($dictionary->get($message), $args);
    }
    
    /**
     * Encapsulate message as translated message with ## ##
     *
     * @param string $message
     * @return string
     */
    public static function _runtime($message) {
        return '##' . $message . '##';
    }
    
    /**
     * Translates text enclosed in ##any text##
     * This function is not parsed by language parser, because as input should be used e.g. texts loaded from database
     *
     * @param string $message String to translate
     * @return string Translated text
     */
    public static function _localizeRuntime($message, $langCode = '') {
        preg_match_all('/##(.+?)##/ms', $message, $attributes, PREG_OFFSET_CAPTURE);
        foreach ($attributes[1] as $index => $attribute) {
            $message = str_replace($attributes[0][$index][0], self::_($attribute[0], null, $langCode), $message);
        }
        return $message;
        
    }
}

?>
