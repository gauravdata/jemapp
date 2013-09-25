<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: CheckException.class.php 18859 2008-06-26 14:57:55Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Holds the array of all ConstraintExceptions that have been thrown during check on Db_Row
 * 
 * @package GwtPhpFramework
 */
class Gpf_DbEngine_Row_CheckException extends Gpf_Exception implements IteratorAggregate  {
    private $constraintExceptions;

    public function __construct(array $constraintExceptions) {
        $this->constraintExceptions = $constraintExceptions;
        parent::__construct($this->buildMessage());
    }
    
    protected function logException() {        
    }
    
    public function getConstraintExceptions() {
        return $this->constraintExceptions;
    }
    
    public function isEmpty() {
        return count($this->constraintExceptions) == 0;
    }
    
    /**
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->constraintExceptions);
    }
    
    private function buildMessage() {
        $message = "";
        foreach ($this->constraintExceptions as $constraintException) {
            $message .= $constraintException->getMessage() . ", ";
        }
        return rtrim($message, ", ");
    }
}

?>
