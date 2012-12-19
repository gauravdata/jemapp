<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Table.class.php 20488 2008-09-02 12:52:19Z mbebjak $
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
abstract class Gpf_DbEngine_DeleteConstraint extends Gpf_Object {

    /**
     * @var array of string
     */
    protected $selfColumns;
    /**
     * @var array of string
     */
    protected $foreignColumns;
    /**
     * @var Gpf_DbEngine_Row
     */
    protected $foreignDbRow;
    
    function __construct($selfColumns, $foreignColumns, Gpf_DbEngine_Row $foreignDbRow) {
        if (is_array($selfColumns)) {
            $this->selfColumns = $selfColumns;            
        } else {
            $this->selfColumns = array($selfColumns);
        }
        if (is_array($foreignColumns)) {
            $this->foreignColumns = $foreignColumns;            
        } else {
            $this->foreignColumns = array($foreignColumns);
        }
        if (count($this->selfColumns) != count($this->foreignColumns)) {
            throw new Gpf_Exception("selfColumns count and foreignColumnsCount must be equal when creating DeleteConstraint");
        }
        $this->foreignDbRow = $foreignDbRow;
    }
    
    abstract public function execute(Gpf_DbEngine_Row $dbRow);
    
}
