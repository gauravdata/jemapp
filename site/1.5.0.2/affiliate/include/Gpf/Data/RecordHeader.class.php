<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RecordHeader.class.php 24940 2009-07-14 14:50:18Z mgalik $
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
class Gpf_Data_RecordHeader extends Gpf_Object {
    private $ids = array();
    
    /**
     * Create Record header object
     *
     * @param array $headerArray
     */
    public function __construct($headerArray = null) {
        if($headerArray === null) {
            return;
        }
        
        foreach ($headerArray as $id) {
            $this->add($id);
        }
    }
    
    public function contains($id) {
        return array_key_exists($id, $this->ids);
    }

    public function add($id) {
        if($this->contains($id)) {
            return;
        }

        $this->ids[$id] = count($this->ids);
    }

    public function getIds() {
        return array_keys($this->ids);
    }

    public function getIndex($id) {
        if(!$this->contains($id)) {
            throw new Gpf_Exception("Unknown column '" . $id ."'");
        }
        return $this->ids[$id];
    }
    
    public function getSize() {
        return count($this->ids);
    }

    public function toArray() {
        $response = array();
        foreach ($this->ids as $columnId => $columnIndex) {
            $response[] = $columnId;
        }
        return $response;
    }
        
    public function toObject() {
        $result = array();
        foreach ($this->ids as $columnId => $columnIndex) {
            $result[] = $columnId;
        }
        return $result;
    }
}

?>
