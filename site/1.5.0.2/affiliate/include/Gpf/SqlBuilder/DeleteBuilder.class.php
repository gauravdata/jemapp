<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DeleteBuilder.class.php 29088 2010-08-16 10:58:32Z iivanco $
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
class Gpf_SqlBuilder_DeleteBuilder extends Gpf_SqlBuilder_ModifyBuilder {
	
	/**
	 * @var Gpf_SqlBuilder_DeleteClause
	 */
	public $delete;
    /**
     * @var Gpf_SqlBuilder_FromClause
     */
    public $from;
    /**
     * @var Gpf_SqlBuilder_WhereClause
     */
    public $where;
    /**
     * @var Gpf_SqlBuilder_LimitClause
     */
    public $limit;

    function __construct() {
    	$this->delete = new Gpf_SqlBuilder_DeleteClause();
        $this->from = new Gpf_SqlBuilder_FromClause();
        $this->where = new Gpf_SqlBuilder_WhereClause();
        $this->limit = new Gpf_SqlBuilder_LimitClause();
    }

    public function toString() {
        return "DELETE ".
        $this->delete->toString().
        "FROM ".
        $this->from->toString().
        $this->where->toString().
        $this->limit->toString();
    }

    public function delete() {
        return $this->execute();
    }

    public function deleteOne() {
        $this->executeOne();
    }
}

?>
