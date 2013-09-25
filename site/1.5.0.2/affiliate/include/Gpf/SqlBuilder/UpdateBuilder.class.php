<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateBuilder.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_SqlBuilder_UpdateBuilder extends Gpf_SqlBuilder_ModifyBuilder {
    public $tableName;

    /**
     * @var Gpf_SqlBuilder_FromClause
     */
    public $from;
    /**
     * @var Gpf_SqlBuilder_SetClause
     */
    public $set;
    /**
     * @var Gpf_SqlBuilder_WhereClause
     */
    public $where;

    function __construct() {
        $this->from = new Gpf_SqlBuilder_FromClause();
        $this->set = new Gpf_SqlBuilder_SetClause();
        $this->where = new Gpf_SqlBuilder_WhereClause();
    }

    public function initSelect() {
        if(!empty($this->tableName)) {
            $this->from->add($this->tableName);
        }
    }

    public function toString() {
        if($this->from->isEmpty()) {
            return '';
        }
        return "UPDATE ".
        $this->from->toString() .
        $this->set->toString().
        $this->where->toString();
    }

    public function update() {
        return $this->execute();
    }

    public function updateOne() {
        $this->executeOne();
    }
}

?>
