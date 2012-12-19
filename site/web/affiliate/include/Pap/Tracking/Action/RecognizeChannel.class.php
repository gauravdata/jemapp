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
class Pap_Tracking_Action_RecognizeChannel extends Pap_Tracking_Common_RecognizeChannel {

    /**
     * @return Pap_Db_Channel
     */
    protected function recognizeChannels(Pap_Contexts_Tracking $context) {

        try {
            return $this->getChannelFromParameter($context);
        } catch (Gpf_Exception $e) {
        }

        try {
            $visitorAffiliate = $context->getVisitorAffiliate();
            if ($visitorAffiliate != null) {
                $context->debug('Trying to get channel from visitor affiliate.');
                return $this->getChannelById($context, $visitorAffiliate->getChannelId());
            }
        } catch (Gpf_Exception $e) {
        }
    }

    /**
     * returns campaign object from user ID stored in custom cookie parameter
     */
    private function getChannelFromParameter(Pap_Contexts_Action $context) {
        $context->debug('Trying to get channel from forced parameter '.Pap_Tracking_ActionRequest::PARAM_ACTION_CHANNELID);

        return $this->getChannelById($context, $context->getChannelIdFromRequest());
    }
}

?>
