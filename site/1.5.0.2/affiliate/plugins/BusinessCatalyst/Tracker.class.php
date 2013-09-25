<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Maros Galik
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
 * @package PostAffiliatePro plugins
 */
class BusinessCatalyst_Tracker extends Pap_Tracking_CallbackTracker {

    // https://help.worldsecuresystems.com/catalystwebservice/catalystcrmwebservice.asmx?op=Order_Retrieve

    private $response = '';

    /**
     * @return BusinessCatalyst_Tracker
     */
    public function getInstance() {
        $tracker = new BusinessCatalyst_Tracker();
        $tracker->setTrackerName("BusinessCatalyst");
        return $tracker;
    }

    public function checkStatus() {
        return true;
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }

}

?>
