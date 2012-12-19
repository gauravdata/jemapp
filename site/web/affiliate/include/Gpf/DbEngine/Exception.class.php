<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Exception.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_DbEngine_Exception extends Gpf_Exception  {

    function __construct($message) {
        parent::__construct($message);
    }

    protected function logException() {
        Gpf_Log::disableType(Gpf_Log_LoggerDatabase::TYPE);
        Gpf_Log::error($this->getMessage());
        Gpf_Log::enableAllTypes();
    }
}
?>
