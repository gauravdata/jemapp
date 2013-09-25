<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Gpf_Data_Table extends Gpf_Object implements Gpf_Rpc_Serializable {
    /**
     * @var Gpf_Data_RecordSet
     */
    private $rows;
    private $count;
    private $from;
    private $to;

    public function __construct(Gpf_Rpc_Params $params) {
        $this->from = $params->get("from");
        $this->to = $params->get("to");
    }
    
    public function getFrom() {
        return $this->from;
    }
    
    public function getTo() {
        return $this->to;
    }
    
    public function fill(Gpf_Data_RecordSet $data) {
        $this->rows = $data;
        $this->from = 0;
        $this->to = $this->count = $data->getSize();
    }

    public function loadFromObject(stdClass  $object) {
        $this->rows = new Gpf_Data_RecordSet();
        $this->rows->loadFromObject($object->rows);
        $this->count = $object->count;
        $this->from = $object->from;
        $this->to = $object->to;
    }

    public function setCount($count) {
        $this->count = $count;
    }

    public function setRange($from, $to) {
        $this->from = $from;
        $this->to = $to;
    }
    
    public function setData(Gpf_Data_RecordSet $data) {
        $this->rows = $data;
    }
    
    public function extendRange($extendSize) {
        if (($this->from -= $extendSize) < 0) {
            $this->from = 0;
        }
        if (($this->to += $extendSize) > $this->count) {
            $this->to = $this->count;
        }
   
    }

    public function toObject() {
        $object = new stdClass();
        $object->rows = $this->rows->toObject();
        $object->from = $this->from;
        $object->to = $this->to;
        $object->count = $this->count;
        return $object;
    }

    public function toText() {
        return "$this->from - $this->to ($this->count)";
    }
}

?>
