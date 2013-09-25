<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
 * @package PostAffiliate
 */
class Pap_Contexts_BackwardCompatibility extends Pap_Contexts_Tracking {

    public function __construct() {
        $this->initDebugLogger();
    }

    protected function getActionTypeConstant() {
    	return Pap_Common_Constants::TYPE_ACTION;
    }
    
    public function getUserId() {
        try {
            return $this->getVisitorAffiliate()->getUserId();
        } catch (Gpf_Exception $e) {
            return '';
        }
    }
    
    public function getCampaignId() {
        try {
            return $this->getVisitorAffiliate()->getCampaignId();
        } catch (Gpf_Exception $e) {
            return '';
        }
    }
}
?>
