<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RowBase.class.php 24447 2009-05-18 13:21:27Z mgalik $
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
abstract class Gpf_DbEngine_RowBase extends Gpf_Object implements Gpf_Data_Row, Gpf_Templates_HasAttributes {
    /**
     * @var boolean
     */
    protected $isPersistent;
        
    abstract public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '');
    abstract public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select);
    
    /**
     * @return boolean true if object has been loaded from database, otherwise false
     */
    public function isPersistent() {
        return $this->isPersistent;
    }

    public function setPersistent($persistent) {
        $this->isPersistent = $persistent;
    }
    
    /**
     * Inserts row
     *
     */
    public function insert() {
        throw new Gpf_Exception('Unimplemented');
    }
    
    /**
     *
     */
    public function update($updateColumns = array()) {
        throw new Gpf_Exception('Unimplemented');
    }
    
    /**
     *
     */
    public function load() {
        throw new Gpf_Exception('Unimplemented');
    }
}

?>
