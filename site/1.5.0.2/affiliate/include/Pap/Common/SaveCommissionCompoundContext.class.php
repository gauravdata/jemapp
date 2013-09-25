<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */

class Pap_Common_SaveCommissionCompoundContext {
    /**
     * @var Pap_Contexts_Tracking
     */
    private $context;
    private $tier;
    private $user;
    /**
     * @var Pap_Tracking_Common_SaveAllCommissions
     */
    private $saveAllCommissions;
    
    public function __construct(Pap_Contexts_Tracking $context, $tier, Pap_Common_User $user, Pap_Tracking_Common_SaveAllCommissions $saveAllCommissions){
        $this->context = $context;
        $this->tier = $tier;
        $this->user = $user;
        $this->saveAllCommissions = $saveAllCommissions;
    }
    
    /**
     * @return Pap_Contexts_Tracking
     */
    public function getContext(){
        return $this->context;
    }
    
    public function getTier() {
        return $this->tier;
    }
    
    /**
     * @return Pap_Common_User
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * @return Pap_Tracking_Common_SaveAllCommissions
     */
    public function getSaveObject() {
        return $this->saveAllCommissions;
    }
}

?>
