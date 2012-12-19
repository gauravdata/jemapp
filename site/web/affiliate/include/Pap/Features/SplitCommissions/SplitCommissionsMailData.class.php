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
class Pap_Features_SplitCommissions_SplitCommissionsMailData extends Gpf_Object {

    /**
     * @var Pap_Common_Transaction
     */
    protected $transaction;

    protected $name;

    protected $email;

    /**
     *
     * @return Pap_Common_User
     */
    protected function loadUser($userId) {
        return Pap_Common_User::getUserById($userId);
    }

    protected function setAttributes($name, $email, Pap_Common_Transaction $transaction) {
        $this->name = $name;
        $this->email = $email;
        $this->transaction = $transaction;
    }

    public function __construct(Pap_Common_Transaction $transaction) {
        try {
            $user = $this->loadUser($transaction->getUserId());
            $this->setAttributes($user->getName(), $user->getEmail(), $transaction);
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::warning('User with userid=' . $transaction->getUserId() . ' was not found!');
            $this->setAttributes($this->_('Unknown user'), $this->_('Unknown email'), $transaction);
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getCommission() {
        return $this->transaction->getCommission();
    }

    public function getCommissionSplit() {
        return $this->transaction->getSplit()*100;
    }

    public function getStatusCode() {
        return $this->transaction->getStatus();
    }

    public function getStatus() {
        $constants = new Pap_Common_Constants();
        return $constants->getStatusAsText($this->transaction->getStatus());
    }

    public function getRefererUrl() {
        return $this->transaction->getRefererUrl();
    }

    public function getLastClickTime() {
        return $this->transaction->getLastClickTime();
    }
    
    public function getTier() {
        return $this->transaction->getTier();
    }
    
    public function getDbRow() {
        return $this->transaction;
    }
}

?>
