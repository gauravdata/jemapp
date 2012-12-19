<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Stats_Base extends Pap_Stats_Data_Object {
    
    /**
     * @var Pap_Stats_Params
     */
    protected $params;
    
    public function __construct(Pap_Stats_Params $params) {
        parent::__construct();
        $this->params = $params;
    }
}
?>
