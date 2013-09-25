<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SetClause.class.php 22094 2008-11-04 13:45:10Z mjancovic $
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
class Gpf_SqlBuilder_SetClause extends Gpf_Object {
    private $clause = array();

    public function add($column, $value, $doQuote = true) {
        $i = count($this->clause);
        $this->clause[$i]['column'] = $column;
        $this->clause[$i]['value']  = $value;
        $this->clause[$i]['doQuote']  = $doQuote;
    }

    public function addDontQuote($column, $value) {
        $this->add($column, $value, false);
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $column) {
        	
        	$out .= $column['column'] . ' = ';
        	
            if ($column['doQuote']) {
                $out .= "'" . $this->createDatabase()->escapeString($column['value']) . "'";
            } else {
                if ($column['value'] == NULL) {
                    $out .= "NULL";
                } else {
                    $out .= $column['value'];
                }
            }
            $out .= ',';
        }
        return ' SET ' . rtrim($out, ','). ' ';
    }

}

?>
