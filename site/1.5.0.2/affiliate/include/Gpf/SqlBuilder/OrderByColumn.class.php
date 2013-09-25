<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: OrderByColumn.class.php 27737 2010-04-12 07:49:56Z mbebjak $
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
class Gpf_SqlBuilder_OrderByColumn extends Gpf_Object {
    private $name;
    private $asc;
    private $tableName;

    public function Gpf_SqlBuilder_OrderByColumn($name, $asc = true, $tableName = '') {
        $this->name = $name;
        $this->asc = $asc;
        $this->tableName = $tableName;
    }
    
    public function getName() {
        return $this->name;
    }

    public function toString() {
        $out = '';
        if(!empty($this->tableName)) {
            $out = $this->tableName . '.';
        }
        $out .= $this->name;
        if($this->asc) {
            $out .= ' ASC';
        } else {
            $out .= ' DESC';
        }
        return $out;
    }

}

?>
