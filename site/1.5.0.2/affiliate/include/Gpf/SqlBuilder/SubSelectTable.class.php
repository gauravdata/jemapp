<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: JoinTable.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_SqlBuilder_SubSelectTable extends Gpf_Object implements Gpf_SqlBuilder_FromClauseTable {
    private $alias;
    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    private $subSelect;

    public function __construct(Gpf_SqlBuilder_SelectBuilder $subSelect, $alias) {
        $this->alias = $alias;
        $this->subSelect = $subSelect;
    }

    public function getAlias() {
        return $this->alias;
    }

    /**
     * 
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getSubSelect() {
        return $this->subSelect;
    }
    
    public function isJoin() {
        return false;
    }
    
    public function toString() {
        return '('. $this->subSelect->toString() .') ' . $this->alias;
    }
}

?>
