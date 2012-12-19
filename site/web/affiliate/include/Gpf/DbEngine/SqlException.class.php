<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SqlException.class.php 19050 2008-07-09 13:23:41Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_DbEngine_SqlException extends Gpf_DbEngine_Exception  {
    protected $_code;
    private $isLoggerException = false;

    function __construct($sqlString, $message, $code) {
        $this->isLoggerException = Gpf_Log_Logger::isLoggerInsert($sqlString);
        $this->_code = $code;
        parent::__construct("ERROR: " . $message);
    }

    protected function logException() {
        if ($this->isLoggerException) {
            parent::logException();
            return;
        }
        Gpf_Log::error($this->getMessage());
    }

    abstract function isDuplicateEntryError();
}
?>
