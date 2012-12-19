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
class Pap_Stats_Data_Commission extends Pap_Stats_Data_Object {

    private $approved;
    private $pending;
    private $declined;
    private $paid;
    
    public function __construct($approved = 0, $pending = 0, $declined = 0, $paid = 0) {
        parent::__construct();
        $this->init($approved, $pending, $declined, $paid);
    }
    
    protected function init($approved = 0, $pending = 0, $declined = 0, $paid = 0) {
        $this->approved = $approved;
        $this->pending = $pending;
        $this->declined = $declined;
        $this->paid = $paid;
    }
    
    protected function getValueNames() {
        return array('approved', 'pending', 'declined', 'paid', 'all');
    }

    public function getAll() {
        return $this->approved + $this->pending + $this->declined + $this->paid;
    }
    
    public function getApproved() {
        return $this->approved;
    }
    
    public function getPending() {
        return $this->pending;
    }
    
    public function getDeclined() {
        return $this->declined;
    }
    
    public function getPaid() {
        return $this->paid;
    }
    
    function addApproved($count) {
        $this->approved += $count;
    }
    
    function addPending($count) {
        $this->pending += $count;
    }
    
    function addDeclined($count) {
        $this->declined += $count;
    }
    
    function addPaid($count) {
        $this->paid += $count;
    }
    
    function add(Pap_Stats_Data_Commission $commission) {
        $this->addApproved($commission->getApproved());
        $this->addPending($commission->getPending());
        $this->addDeclined($commission->getDeclined());
        $this->addPaid($commission->getPaid());
    }
}
?>
