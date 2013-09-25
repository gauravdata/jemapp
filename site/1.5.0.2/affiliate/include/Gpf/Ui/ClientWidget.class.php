<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ClientWidget.class.php 20051 2008-08-21 16:21:36Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Gpf_Ui_ClientWidget extends Gpf_Object {
    const WIDGET_ID = 'widgetId';
    private $name;
    /**
     * @var Gpf_Data_RecordSet
     */
    private $data;
    /**
     * @var Gpf_Data_Record
     */
    private $lastRecord;
    
    private $id;
    
    public function __construct($name, array $dataHeader) {
        $this->id = 0;
        $this->name = $name;
        $this->data = new Gpf_Data_RecordSet();
        $this->data->setHeader(array_merge(array(self::WIDGET_ID),$dataHeader));
    }
    
    public function addData(Gpf_Data_Row $record) {
        $this->lastRecord = $this->data->createRecord();
        $this->lastRecord->set(self::WIDGET_ID, $this->id++ . "");
        foreach ($this->lastRecord as $name => $value) {
            if ($name == self::WIDGET_ID) {
                continue;
            }
            if($this->lastRecord->contains($name)) {
                $this->lastRecord->set($name, $record->get($name) . "");
            }
        }
        $this->data->addRecord($this->lastRecord);
    }
    
    public function getLastId() {
        return $this->getName() . $this->lastRecord->get(self::WIDGET_ID);
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function toObject() {
        return $this->data->toObject();
    }
}
?>
