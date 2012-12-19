<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DataRecordSet.class.php 25631 2009-10-08 14:29:55Z mbebjak $
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
class Gpf_Chart_DataRecordSet {
    private $minValue = 0;
    private $maxValue = 0;
    private $values = array();
    private $name;
    private $color;
    private $tooltip;
    
    public function __construct($name, $color) {
        $this->name = $name;
        $this->color = $color;
        $this->tooltip = "#x_label#<br>$this->name: #val#";
    }
        
    public function addValue($value) {
    	if($value < $this->minValue ) {
    		$this->minValue = $value;
    	}
    	if($value > $this->maxValue ) {
    		$this->maxValue = $value;
    	}
    	$this->values[] = $value;
    }
    
    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;
    }
    
    public function getTooltip() {
        return $this->tooltip;
    }
    
    public function getMinValue() {
    	return $this->minValue;
    }

    public function getMaxValue() {
    	return $this->maxValue;
    }
    
    public function getValues() {
        return $this->values;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getColor() {
        return $this->color;
    }
    
    public function getSize() {
        return count($this->values);
    }
    
    public function fill(Gpf_Chart_Labels $labels, array $data) {
        foreach ($labels->getLabels() as $label) {
            if (array_key_exists($label, $data)) {
                $this->addValue((int) $data[$label]);
                continue;
            }
            $this->addValue(0);
        }
    }
}

?>
