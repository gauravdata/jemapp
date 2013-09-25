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
class Pap_Stats_Data_Impression extends Pap_Stats_Data_Object {

    private $raw;
    private $unique;
    
    public function __construct($raw, $unique) {
        parent::__construct();
        $this->raw = ($raw == null || $raw == '') ? 0 : $raw;
        $this->unique = ($unique == null || $unique == '') ? 0 : $unique;
    }
    
    protected function getValueNames() {
        return array('raw', 'unique', 'all');
    }

    public function getAll() {
        return $this->getRaw();
    }
    
    public function getRaw() {
        return $this->raw;
    }
    
    public function getUnique() {
        return $this->unique;
    }
}
?>
