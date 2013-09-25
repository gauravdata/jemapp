<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Common_Reports_Chart_ImpressionDataType extends Pap_Common_Reports_Chart_BaseDataType {
        
    public function __construct() {
        parent::__construct('impressionCount', $this->_('Number of Impressions'));
    }
    
    /**
     * @return Pap_Stats_Computer_Graph_Base
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        return new Pap_Stats_Computer_Graph_Impressions($statsParameters, $timeGroupBy);
    }
}

?>
