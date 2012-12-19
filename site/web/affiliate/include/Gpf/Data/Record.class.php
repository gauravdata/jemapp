<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Record.class.php 20060 2008-08-21 19:02:52Z aharsani $
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
class Gpf_Data_Record extends Gpf_Object implements Iterator, Gpf_Rpc_Serializable, 
    Gpf_Templates_HasAttributes, Gpf_Data_Row {
    private $record;
    /**
     *
     * @var Gpf_Data_RecordHeader
     */
    private $header;
    private $position;

    /**
     * Create record
     *
     * @param array $header
     * @param array $array values of record from array
     */
    public function __construct($header, $array = array()) {
        if (is_array($header)) {
            $header = new Gpf_Data_RecordHeader($header);
        }
        $this->header = $header;
        $this->record = array_values($array);
        while(count($this->record) < $this->header->getSize()) {
            $this->record[] = null;
        }
    }
    
    function getAttributes() {
        $ret = array();
        foreach ($this as $name => $value) {
            $ret[$name] = $value;
        }
        return $ret;
    }
    
    public function contains($id) {
        return $this->header->contains($id);    
    }
    
    public function get($id) {
        $index = $this->header->getIndex($id);
        return $this->record[$index];
    }

    public function set($id, $value) {
        $index = $this->header->getIndex($id);
        $this->record[$index] = $value;
    }
    
    public function add($id, $value) {
        $this->header->add($id);
        $this->set($id, $value);
    }
    
    public function toObject() {
        return $this->record;
    }
    
    public function loadFromObject(array $array) {
        $this->record = $array;
    }
    
    public function toText() {
        return implode('-', $this->record);
    }

    public function current() {
        if(!isset($this->record[$this->position])) {
            return null;
        }
        return $this->record[$this->position];
    }

    public function key() {
        $ids = $this->header->getIds();
        return $ids[$this->position];
    }

    public function next() {
        $this->position++;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return $this->position < $this->header->getSize();
    }
}

?>
