<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SelectColumn.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_SqlBuilder_SelectColumn extends Gpf_Object {
    private $name;
    private $alias;
    private $tableName;
    private $doQuote = false;

    function __construct($name, $alias = '', $doQuote = false) {
        $this->name = $name;
        $this->alias = $alias;
        $this->doQuote = $doQuote;
    }
    
    public function setTablePrefix($prefix) {
        if(strlen($prefix)) {
            $this->tableName = $prefix;
        }
    }
    
    public function toString() {
        $out = '';
        if(!empty($this->tableName)) {
            $out = $this->tableName . '.';
        }
        if ($this->doQuote) {
            $out .= "'" .  $this->name . "'";
        } else {
            $out .= $this->name;
        }
        if(!empty($this->alias)) {
            $out .= ' AS ' . $this->alias;
        }
        return $out;
    }

    public function getAlias() {
        return $this->alias;
    }
    
    public function getName() {
        return $this->name;
    }
}

?>
