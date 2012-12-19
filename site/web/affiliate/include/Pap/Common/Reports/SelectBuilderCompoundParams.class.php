<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Common_Reports_SelectBuilderCompoundParams {

    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    private $selectBuilder = null;
    
    /**
     * @var Pap_Stats_Params
     */
    private $params = null;

    public function __construct(Gpf_SqlBuilder_SelectBuilder $selectBuilder, Pap_Stats_Params $params) {
        $this->selectBuilder = $selectBuilder;
        $this->params = $params;
    }

    /**
     * @return Pap_Stats_Params
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getSelectBuilder() {
        return $this->selectBuilder;
    }
}
?>
