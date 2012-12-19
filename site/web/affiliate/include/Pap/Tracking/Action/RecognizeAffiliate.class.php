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
class Pap_Tracking_Action_RecognizeAffiliate extends Pap_Tracking_Common_RecognizeAffiliate implements Pap_Tracking_Common_Recognizer {

    protected function getUser(Pap_Contexts_Tracking $context) {
        if ($context->getUserObject() != null) {
            return $context->getUserObject();
        }

        if (($user = $this->getUserFromParameter($context)) != null) {
            return $user;
        }

        if (($user = $this->getUserFromVisitorAffiliate($context)) != null) {
            return $user;
        }

        if (($user = $this->getDefaultAffiliate($context)) != null) {
            return $user;
        }

        return null;
    }

    /**
     * @return Pap_Common_User
     */
    private function getUserFromVisitorAffiliate(Pap_Contexts_Action $context) {
        $context->debug('Getting user from visitor affiliate');
        try {
            return $this->getCorrectUser($context, $context->getVisitorAffiliate()->getUserId(), $context->getVisit()->getTrackMethod());
        } catch (Gpf_Exception $e) {
            $context->debug('User not recognized from visitor affiliate');
            return null;
        }
    }

    /**
     * returns user object from user ID stored in request parameter
     */
    private function getUserFromParameter(Pap_Contexts_Action $context) {
        $context->debug("    Trying to get affiliate from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_AFFILIATEID."'");

        $userId = $context->getAffiliateIdFromRequest();
        if($userId != '') {
            return $this->getCorrectUser($context, $userId, Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER);
        }

        $context->debug("        Affiliate not found in parameter");
        return null;
    }



    /**
     * returns user object from user ID stored in default affiliate
     *
     * @return string
     */
    protected function getDefaultAffiliate(Pap_Contexts_Action $context) {
        $context->debug("Trying to get default affiliate");
        if (Gpf_Settings::get(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME) != Gpf::YES) {
            $context->debug("Save unreferred sale is not enabled");
            return null;
        }
        $userId = Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME);
        if($userId == '') {
            $context->debug("No default affiliate defined");
            return null;
        }

        return $this->getCorrectUser($context, $userId, Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE);
    }

    /**
     * checks that user with this ID exists and is correct
     *
     * @param Pap_Contexts_Action $context
     * @param string $userId
     * @param string $trackingMethod
     * @return Pap_Common_User
     */
    protected function getCorrectUser(Pap_Contexts_Action $context, $userId, $trackingMethod) {
        $context->debug('Checking affiliate with Id: '.$userId);
        $userObj = $this->getUserById($context, $userId);
        if($userObj == null) {
            return null;
        }
        if ($context->getTrackingMethod() == '') {
            $context->setTrackingMethod($trackingMethod);
        }
        return $userObj;
    }
}
?>
