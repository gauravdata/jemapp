<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Tracking_Click_RecognizeChannel extends Pap_Tracking_Common_RecognizeChannel implements Pap_Tracking_Common_Recognizer {

    /**
     * @return Pap_Db_Channel
     */
    protected function recognizeChannels(Pap_Contexts_Tracking $context) {
        try {
            return $this->getChannelFromForcedParameter($context);
        } catch (Gpf_Exception $e) {
        }

        try {
            return $this->getChannelFromParameter($context);
        } catch (Gpf_Exception $e) {
        }
    }

    /**
     * @return Pap_Db_Channel
     * @throws Gpf_Exception
     */
    private function getChannelFromForcedParameter(Pap_Contexts_Click $context) {
        $context->debug('Trying to get channel from forced parameter');
        return $this->getChannelById($context, $context->getForcedChannelId());
    }

    /**
     * @return Pap_Db_Channel
     * @throws Gpf_Exception
     */
    private function getChannelFromParameter(Pap_Contexts_Click $context) {
        $context->debug('Trying to get channel from parameter');
        return $this->getChannelById($context, $context->getChannelId());
    }
}

?>
