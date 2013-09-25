<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DeleteClause.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_SqlBuilder_DeleteClause extends Gpf_Object {
    private $clause = array();

    public function add($tableAlias = null) {
    	if ($tableAlias == null) {
    		throw new Gpf_Exception($this->_("Table alias must by defined"));
    	}
        $this->clause[] = $tableAlias;
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $deleteAlias) {
            $out .= $out ? ',' : '';
            $out .= $deleteAlias;
        }
        if(empty($out)) {
            return '';
        }
        
        return $out." ";
    }
}

?>
