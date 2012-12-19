<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Operator.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Gpf_SqlBuilder_Operator extends Gpf_Object {
    
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $sqlCode;
    /**
     * @var boolean
     */
    private $doQuote;
    
    public function __construct($code, $name, $sqlCode, $doQuote) {
        $this->code = $code;
        $this->name = $name;
        $this->sqlCode = $sqlCode;
        $this->doQuote = $doQuote;
    }
    
    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }
    
    /**
     * @return string
     */
    public function getSqlCode() {
        return $this->sqlCode;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getDoQuote() {
        return $this->doQuote;
    }
}
?>
