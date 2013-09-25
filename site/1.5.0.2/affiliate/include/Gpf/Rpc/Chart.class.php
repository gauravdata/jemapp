<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Chart.class.php 22526 2008-11-27 12:21:55Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

// TODO: code is made ready for yAxisRight but it does not work in current version

/**
 * @package GwtPhpFramework
 */
class Gpf_Rpc_Chart extends Gpf_Object implements Gpf_Rpc_Serializable {
       
    const CHART_TYPE_LINE = "line";
    const CHART_TYPE_LINE_DOT = "lineDot";
    const CHART_TYPE_BAR_OUTLINE = "bar";
    const CHART_TYPE_AREA = "area";

    /*
     * preferred number of value tags on X axis 
     */
    const CONST_X_VALUES = 6;
    /*
     * preferred number of value tags on Y axis 
     */
    const CONST_Y_VALUES = 5;
    
    private $min1 = 0, $min2 = 0, $max1 = 0, $max2 = 0;
    private $stepsX = 1, $stepsY1 = 1, $stepsY2 = 1;
    /**
     * @var Gpf_Chart_DataRecordSet
     */
    private $data1, $data2;
    
    /**
     * @var array
     */
    private $labels;
    
    /**
     * @var OFC_Chart
     */
    private $chart;
    private $lineColors = array(1 => "0x0568AD", 2 => "0x9933CC");
    
    private $chartType;
    
    public function __construct() {
        $this->data1 = null;
        $this->data2 = null;
        
        $this->chart = new OFC_Chart();
        $this->chart->set_bg_colour('#FFFFFF');
        $title = new OFC_Elements_Title('');
        $title->set_style('font-size: 14px;');
        $this->setTitle($title);
    }
    
    public function setLabels(array $labels) {
    	$this->labels = $labels;
    }
    
  
    public function addData1Recordset(Gpf_Chart_DataRecordSet $data) {
        $this->data1 = $data;
    }

    public function addData2Recordset(Gpf_Chart_DataRecordSet $data) {
        $this->data2 = $data;
    }
    
    public function getLineColor($i) {
        return $this->lineColors[$i];
    }
    
    public function toObject() {
        $this->consolidateParameters();
        
        $xAxisLabels = new OFC_Elements_Axis_X_Label_Set();
        $xAxisLabels->set_labels($this->labels);
        $xAxisLabels->set_size(10);
        $xAxisLabels->set_colour('#444444');      
        $xAxisLabels->set_steps($this->stepsX);
        
        $xAxis = new OFC_Elements_Axis_X();
        $xAxis->set_steps($this->stepsX);
        $xAxis->set_labels($xAxisLabels);
        $xAxis->set_colour('#CCCCCC');
        $xAxis->set_grid_colour('#AAAAAA');
        $this->chart->set_x_axis($xAxis);
        
        $yAxis = new OFC_Elements_Axis_Y();
        $yAxis->set_range($this->min1, $this->max1);
        $yAxis->set_colour('#AAAAAA');
        $yAxis->set_grid_colour('#AAAAAA');
        $yAxis->set_steps($this->stepsY1);
        $this->chart->set_y_axis($yAxis);
        
        $yLegend = new OFC_Elements_Legend_Y($this->data1->getName());
        //$this->chart->set_y_legend($yLegend); // does not work now

        // data 1
        $this->chart->add_element($this->createGraph($this->data1));
        
        if($this->hasTwoDataLines()) {
            $yAxisRight = new OFC_Elements_Axis_Y_Right();
            $yAxisRight->set_range($this->min2, $this->max2);
            $yAxisRight->set_colour('#AAAAAA');
            $yAxisRight->set_steps($this->stepsY2);
//            $this->chart->set_y_axis_right($yAxisRight);
            
            $this->chart->add_element($this->createGraph($this->data2));
        }
        
        return $this->chart;
    }
    
    public function toText() {
        throw new Gpf_Exception("unimplemented");
    }

    public function setErrorMessage($message) {
        $this->errorMessage = $message;
    }
    
    private function consolidateParameters() {
        if(!$this->labels) {
        	$this->labels = array();
        }
        
        $this->computeStepsAndMinMax();
    }
    
    private function computeStepsAndMinMax() {
        if($this->data1->getSize() >= (self::CONST_X_VALUES + 5)) {       
            $this->stepsX = round($this->data1->getSize() / self::CONST_X_VALUES);
        }
        if($this->data1->getMaxValue() > self::CONST_Y_VALUES) {       
            $this->stepsY1 = ceil(($this->data1->getMaxValue()+1) / self::CONST_Y_VALUES);
        }
		$this->min1 = $this->data1->getMinValue();
		$this->max1 = $this->adjustMaxValue($this->data1->getMaxValue(), $this->stepsY1);
		
		if ($this->hasTwoDataLines()) {
		    if($this->data2->getMaxValue() > self::CONST_Y_VALUES) {       
                $this->stepsY2 = ceil(($this->data2->getMaxValue()+1) / self::CONST_Y_VALUES);
            }		
		    $this->min2 = $this->data2->getMinValue();
            $this->max2 = $this->adjustMaxValue($this->data2->getMaxValue()+1, $this->stepsY2);
			
            // if the numbers are small or almost equal, make it equal
//            if ($this->max2 > 0) {
//                $ratio = abs($this->max2/$this->max1);
//            } else {
//                $ratio = 0;
//            }
//			if (($this->max1 <= 20 && $this->max2 <= 20) || ($ratio >= 0.7 && $ratio <= 1.3)) {
				$this->max2 = $this->max1 = max($this->max1, $this->max2);
				$this->stepsY2 = $this->stepsY1 = max($this->stepsY1, $this->stepsY2);
//			}
		}
    }
    
    private function adjustMaxValue($value, $steps) {
        if ($value < self::CONST_Y_VALUES) {
            return self::CONST_Y_VALUES;
        }
        return $steps * self::CONST_Y_VALUES;
    }
    
    private function hasTwoDataLines() {
    	if($this->data2 != null) {
    		return true;
    	} else {
    		return false;
    	}
    }  
	
    private function createGraph(Gpf_Chart_DataRecordSet $data) {
        if($this->chartType == Gpf_Rpc_Chart::CHART_TYPE_LINE_DOT && $this->data1->getSize() > 150) {
                $this->chartType = Gpf_Rpc_Chart::CHART_TYPE_LINE;
        }
        
        switch ($this->chartType) {
            case Gpf_Rpc_Chart::CHART_TYPE_AREA:
                if ($data->getSize() > 40) {
                    $areaGraph = new OFC_Charts_Area_Line();
                } else {
                    $areaGraph = new OFC_Charts_Area_Hollow();
                }
                $areaGraph->set_width(3);
                $areaGraph->set_dot_size(3);
                $areaGraph->set_colour($data->getColor());
                $areaGraph->set_key($data->getName(), 10);
                $areaGraph->set_values($data->getValues());
                $areaGraph->set_tooltip($data->getTooltip());
                return $areaGraph;
            case Gpf_Rpc_Chart::CHART_TYPE_BAR_OUTLINE:
                $barGraph = new OFC_Charts_Bar();
                $barGraph->set_alpha(50);
                $barGraph->set_colour($data->getColor());
                $barGraph->set_key($data->getName(), 10);
                $barGraph->set_values($data->getValues());
                $barGraph->set_tooltip($data->getTooltip());
                return $barGraph;
            case Gpf_Rpc_Chart::CHART_TYPE_LINE_DOT:
                $lineDotGraph = new OFC_Charts_Line_Dot();
                $lineDotGraph->set_width(3);
                $lineDotGraph->set_dot_size(3);
                $lineDotGraph->set_colour($data->getColor());
                $lineDotGraph->set_key($data->getName(), 10);
                $lineDotGraph->set_values($data->getValues());
                $lineDotGraph->set_tooltip($data->getTooltip());
                return $lineDotGraph;
            default:
                $lineGraph = new OFC_Charts_Line();
                $lineGraph->set_width(3);
                $lineGraph->set_dot_size(3);
                $lineGraph->set_colour($data->getColor());
                $lineGraph->set_key($data->getName(), 10);
                $lineGraph->set_values($data->getValues());
                $lineGraph->set_tooltip($data->getTooltip());
                return $lineGraph;
        }
    }
    
	public function setTitle(OFC_Elements_Title $title) {
	    $this->chart->set_title($title);
	}
	
	public function setChartType($chartType) {
	    $this->chartType = $chartType;
	}
}

?>
