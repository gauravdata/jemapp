<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani ,Rene dohan
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Exception.class.php 22920 2008-12-21 14:05:22Z rdohan $
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
class Gpf_Exception extends Exception {

    private $id;

    public function __construct($message,$code = null) {
        parent::__construct($message,$code);
    }

    protected function logException() {
        Gpf_Log::error($this->getMessage());
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

}
?>
