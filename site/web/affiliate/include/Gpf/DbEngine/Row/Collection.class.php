<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Collection.class.php 27196 2010-02-11 15:37:10Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * This class represents collection of Db_Row objects
 *
 * @package GwtPhpFramework
 */
class Gpf_DbEngine_Row_Collection extends Gpf_Object implements IteratorAggregate {

    /**
     * @var array of Gpf_DbEngine_RowBase
     */
    protected $rows = array();

    public function add(Gpf_DbEngine_RowBase $row) {
        $this->rows[] = $row;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->rows);
    }

    public function getSize() {
        return count($this->rows);
    }

    /**
     * @return Gpf_DbEngine_RowBase
     */
    public function get($i) {
        return $this->rows[$i];
    }

    public function set($i, Gpf_DbEngine_RowBase $row) {
        $this->rows[$i] = $row;
    }

    public function remove($i) {
        unset($this->rows[$i]);
    }

    public function insert($i, Gpf_DbEngine_RowBase $row) {
        array_splice($this->rows, $i, 0, array($row));
    }
}
?>
