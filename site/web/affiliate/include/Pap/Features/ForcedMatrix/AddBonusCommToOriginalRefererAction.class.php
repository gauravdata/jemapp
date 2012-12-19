<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Features_ForcedMatrix_AddBonusCommToOriginalRefererAction extends Pap_Features_PerformanceRewards_Action_AddBonusCommAction {
    
    public function execute() {
        Gpf_Log::debug('Executing rule: Add bonus commission to original parent affiliate for every sale...');
        if ($this->transaction->getType()!=Pap_Common_Transaction::TYPE_EXTRA_BONUS && $this->transaction->getTier()==1) {
            Gpf_Log::debug('Okay, this is 1st tier, not bonus commission, so we can create new bonus.');
            $this->addBonus();
        }
    }
    
    protected function getCurrentUserId() {
        $userid = $this->rule->getCondition()->getCurrentUserId();
        $user = new Pap_Db_User();
        $user->setId($userid);
        try {
            $user->load();
        } catch (Gpf_Exception $e) {
            return $userid;
        }
        
        return $user->getOriginalParentUserId();
    }
    
    public static function toString() {
        return Gpf_Lang::_("add bonus commission to original referer for every sale");
    }
    
    public function getString() {
        return Gpf_Lang::_('add bonus commission %s to original referer for every sale', $this->getFormattedBonusValue());
    }
}
?>
