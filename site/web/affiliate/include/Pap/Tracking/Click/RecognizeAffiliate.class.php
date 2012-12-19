<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric, Maros Galik
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
class Pap_Tracking_Click_RecognizeAffiliate extends Pap_Tracking_Common_RecognizeAffiliate {


    public function recognize(Pap_Contexts_Tracking $context) {
        if(($context->getUserObject()) != null) {
            $context->debug('  User already recognized, finishing user recognizer');
            return;
        }

        parent::recognize($context);
    }


    protected function getUser(Pap_Contexts_Tracking $context) {
        if (($user = $this->getUserFromForcedParameter($context)) != null) {
            return $user;
        }
         
        if(($user = $this->getUserFromParameter($context)) != null) {
            return $user;
        }

        return null;
    }

    /**
     * returns user object from forced parameter AffiliateID
     * parameter name is dependent on track.js, where it is used.
     *
     * @return string
     */
    protected function getUserFromForcedParameter(Pap_Contexts_Click $context) {
        $context->debug("  Trying to get affiliate from request parameter '".Pap_Tracking_Request::getForcedAffiliateParamName()."'");

        $userId = $context->getForcedAffiliateId();
        if($userId != '') {
            $context->debug("    Setting affiliate from request parameter. Affiliate Id: ".$userId);
            return $this->getUserById($context, $userId);
        }

        $context->debug('Affiliate not found in forced parameter');
        return null;
    }

    /**
     * returns user object from standard parameter from request
     *
     * @return string
     */
    protected function getUserFromParameter(Pap_Contexts_Click $context) {
        $parameterName = Pap_Tracking_Request::getAffiliateClickParamName();
        if($parameterName == '') {
            $context->debug("  Cannot get name of request parameter for affiliate ID");
            return null;
        }
         
        $context->debug("  Trying to get affiliate from request parameter '$parameterName'");

        $userId = $context->getAffiliateId();
        if($userId != '') {
            $context->debug("    Setting affiliate from request parameter. Affiliate Id: ".$userId);
            return $this->getUserById($context, $userId);
        }

        $context->debug("    Affiliate not found in parameter");
        return null;
    }
}

?>
