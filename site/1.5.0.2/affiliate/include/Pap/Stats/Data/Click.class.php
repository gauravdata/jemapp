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
class Pap_Stats_Data_Click extends Pap_Stats_Data_Impression {

    private $declined;
    
    public function __construct($raw, $unique, $declined) {
        parent::__construct($raw, $unique);
        $this->declined = ($declined == null || $declined == '') ? 0 : $declined;
    }

    protected function getValueNames() {
        return array_merge(array('declined'), parent::getValueNames());
    }
    
    public function getDeclined() {
        return $this->declined;
    }
    
    public function getAll() {
        return parent::getAll() + $this->declined;
    }
}
?>
