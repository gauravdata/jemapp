<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Tracking_Action_ComputeStatus extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

    public function recognize(Pap_Contexts_Tracking $context) {
        $fpStatus = $context->getFraudProtectionStatus();
        if($fpStatus != null && $fpStatus != '') {
            $context->debug("    Using status '".$fpStatus."' set by fraud protection");
            $context->setStatusForAllCommissions($fpStatus);
            return;
        }
        if($this->getCustomStatus($context)) {
            return;
        }
    }

    protected function getCustomStatus(Pap_Contexts_Action $context) {
        $context->debug("    Trying to get custom status from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_CUSTOM_STATUS."'");

        $status = $context->getCustomStatusFromRequest();
        if($status != '') {
            $context->debug("        Found custom status: ".$status.", checking");
             
            if(in_array($status, array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING, Pap_Common_Constants::STATUS_DECLINED))) {
                $context->debug("        Setting custom status to $status");
                $context->setStatusForAllCommissions($status);
                return true;
            } else {
                $context->debug("        Custom status is incorrect, it must be one of: A, P, D");
            }
        }

        return false;
    }
}

?>
