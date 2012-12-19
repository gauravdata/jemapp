<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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

/**
 * @package GwtPhpFramework
 */
class Gpf_Rpc_ChartResponse extends Gpf_Object implements Gpf_Rpc_Serializable {
    /**
     * @var Gpf_Rpc_Chart
     */
    private $chart;
    private $dataType1;
    private $dataType2;
    
    public function __construct(Gpf_Rpc_Chart $chart, $dataType1, $dataType2='') {
        $this->chart = $chart;
        $this->dataType1 = $dataType1;
        $this->dataType2 = $dataType2;
    }
    
    public function toText() {
        throw new Gpf_Exception('Gpf_Rpc_ChartResponse::toText() not implemented');
    }

    public function toObject() {
        $response = new stdClass();
        $response->dataType1 = $this->dataType1;
        $response->dataType2 = $this->dataType2;
        $response->chart = $this->chart->toObject();
        return $response;
    }

}

?>
