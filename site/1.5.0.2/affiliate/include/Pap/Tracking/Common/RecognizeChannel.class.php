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
abstract class Pap_Tracking_Common_RecognizeChannel extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

    private $channelsCache = array();

    public function __construct() {
    }

    public final function recognize(Pap_Contexts_Tracking $context) {
        $context->debug('Recognizing channel started');

        $channel = $this->recognizeChannels($context);

        if($channel != null) {
            $context->setChannelObject($channel);
        } else {
            $context->debug('No channel recognized!');
        }
         
        $context->debug('Recognizing channel ended');
    }

    /**
     * @return Pap_Db_Channel
     */
    protected abstract function recognizeChannels(Pap_Contexts_Tracking $context);

    /**
     * gets channel by channel id
     * @param $channelId
     * @return Pap_Db_Channel
     * @throws Gpf_Exception
     */
    public function getChannelById(Pap_Contexts_Tracking $context, $channelId) {
        if($channelId == '') {
            $this->logAndThrow($context, 'Channel id is empty');
        }
        $user = $context->getUserObject();
        if ($user == null) {
            $this->logAndThrow($context, 'User is not recognized. Channel can not be found');
        }

        if (isset($this->channelsCache[$channelId])) {
            return $this->channelsCache[$channelId];
        }

        $channel = new Pap_Db_Channel();
        $channel->setPrimaryKeyValue($channelId);
        $channel->setPapUserId($user->getId());
        try {
            $channel->loadFromData(array(Pap_Db_Table_Channels::ID, Pap_Db_Table_Channels::USER_ID));
            $context->debug('Channel found: '.$channel->getName());
            $this->channelsCache[$channelId] = $channel;
            return $channel;
        } catch (Gpf_DbEngine_NoRowException $e) {
            $channel->setValue($channelId);
            $channel->loadFromData(array(Pap_Db_Table_Channels::VALUE, Pap_Db_Table_Channels::USER_ID));
            $this->channelsCache[$channelId] = $channel;
            return $channel;
        }
    }

    /**
     * @param $message
     * @throws Pap_Tracking_Exception
     */
    protected function logAndThrow(Pap_Contexts_Tracking $context, $message) {
        $context->debug($message);
        throw new Pap_Tracking_Exception($message);
    }
}

?>
