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
class Pap_Common_Reports_Chart_ClickDataType extends Pap_Common_Reports_Chart_BaseDataType {

    private $type;

    public function __construct($name, $type = Pap_Db_Table_Clicks::RAW) {
        parent::__construct('clickCount'.$type, $name);
        $this->type = $type;
    }

    /**
     * @return Pap_Stats_Computer_Graph_Base
     */
    public function getComputer(Pap_Stats_Params $statsParameters, $timeGroupBy) {
        return new Pap_Stats_Computer_Graph_Clicks($statsParameters, $timeGroupBy, $this->type);
    }
}

?>
