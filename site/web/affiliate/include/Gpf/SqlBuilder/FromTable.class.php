<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FromTable.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Gpf_SqlBuilder_FromTable extends Gpf_Object implements Gpf_SqlBuilder_FromClauseTable {
    private $name;
    private $alias;

    function __construct($name, $alias = '') {
        $this->name = $name;
        $this->alias = $alias;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getName() {
        return $this->name;
    }

    public function toString() {
        $out = $this->name;
        if(!empty($this->alias)) {
            $out .= ' ' . $this->alias;
        }
        return $out;
    }

    public function isJoin() {
        return false;
    }
}

?>
