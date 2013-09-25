<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Data model for Breadcrumbs included to page rendered on server
 * 
 * @package GwtPhpFramework
 */
class Gpf_Ui_Model_Breadcrumbs extends Gpf_Object implements IteratorAggregate {

    /**
     * Array of entries assigned to breadcrumbs
     *
     * @var array
     */
    private $entries = array();
    
    /**
     * Add breadcrumb entry
     *
     * @param string $name
     * @param string $url
     */
    public function add($name, $url) {
        $this->entries[$url] = $name; 
    }
    
    /**
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->entries);
    }
}
?>
