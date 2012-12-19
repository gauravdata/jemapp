<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LimitClause.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Gpf_SqlBuilder_LimitClause extends Gpf_Object {
    private $offset = '';
    private $limit = '';

    public function set($offset, $limit) {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function toString() {
        $out = '';
        if ($this->limit !== '') {
            $out .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== '') {
            $out .= " OFFSET " . $this->offset;
        }
        return $out . " ";
    }
    
    public function isEmpty() {
        return $this->offset === '' && $this->limit === '';
    }
}

?>
