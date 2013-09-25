<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: HavingClause.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_SqlBuilder_HavingClause extends Gpf_SqlBuilder_WhereClause {

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $columnObj) {
            $out .= $out ? $columnObj['operator'] . ' ' : '';
            $out .= $columnObj['obj']->toString() . ' ';
        }
        if(empty($out)) {
            return '';
        }
        return "HAVING $out ";
    }

}

?>
