<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Object.class.php 22676 2008-12-05 12:25:48Z mbebjak $
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
class Gpf_Object {

    /**
     * @return Gpf_DbEngine_Database
     */
    protected function createDatabase() {
        return Gpf_DbEngine_Database::getDatabase();
    }

    /**
     * Translate input message into selected language.
     * If translation will not be found, return same message.
     *
     * @param string $message
     * @return string
     */
    public function _($message) {
        $args = func_get_args();
        return Gpf_Lang::_($message, $args);
    }
    
    /**
     * Translates text enclosed in ##any text##
     * This function is not parsed by language parser, because as input should be used e.g. texts loaded from database
     *
     * @param string $message String to translate
     * @return string Translated text
     */
    public function _localize($message) {
        return Gpf_Lang::_localizeRuntime($message);
    }
    
    /**
     * Translate input message into default language defined in language settings for account.
     * This function should be used in case message should be translated to default language (e.g. log messages written to event log)
     *
     * @param string $message
     * @return string
     */
    public function _sys($message) {
        $args = func_get_args();
        return Gpf_Lang::_sys($message, $args);
    }
}
?>
