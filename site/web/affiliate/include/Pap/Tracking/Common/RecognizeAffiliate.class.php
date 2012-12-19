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
abstract class Pap_Tracking_Common_RecognizeAffiliate extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

    private $usersCache = array();

    public function recognize(Pap_Contexts_Tracking $context) {
        $context->debug('Recognizing affiliate started');

        $user = $this->getUser($context);
         
        if($user == null) {
            $context->debug('    Error, no affiliate recognized! setDoSaveCommissions(false)');
            $context->setDoTrackerSave(false);
            $context->setDoCommissionsSave(false);
            return;
        }

        $context->setUserObject($user);
        $context->debug('Recognizing affiliate ended. Recognized affiliate id: '.$user->getId());
        $context->debug("");
    }

    protected abstract function getUser(Pap_Contexts_Tracking $context);

    protected function addUser($id,Pap_Affiliates_User $user) {
        $this->usersCache[$id] = $user;
    }

    /**
     * gets user by user id
     * @param $userId
     * @return Pap_Affiliates_User
     */
    public function getUserById($context, $id) {
        if($id == '') {
            return null;
        }

        if (isset($this->usersCache[$id])) {
            return $this->usersCache[$id];
        }

        try {
            $this->usersCache[$id] = $this->loadUserFromId($id);
            return $this->usersCache[$id];
        } catch (Gpf_Exception $e) {
            $context->debug("User with RefId/UserId: $id doesn't exist");
            return null;
        }
    }

    protected function loadUserFromId($id) {
        return Pap_Affiliates_User::loadFromId($id);
    }
}

?>
