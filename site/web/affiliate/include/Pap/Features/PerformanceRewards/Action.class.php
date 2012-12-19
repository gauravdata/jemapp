<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
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
abstract class Pap_Features_PerformanceRewards_Action extends Gpf_Object {
    
    const ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP = 'CG';
    const ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP_RETROACTIVELY = 'CGR';
    const ACTION_PUT_ADD_BONUS = 'BC';
    
    /**
     *
     * @var Pap_Features_PerformanceRewards_Rule_Transaction
     */
    protected $rule;
        
    /**
     *
     * @var Pap_Db_Transaction
     */
    protected $transaction;
    
    public function __construct(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        $this->rule = $rule;
        $this->transaction = $rule->getTransaction();
        
        self::createActionsList();
    }
    
    protected static function createActionsList() {
        $list = Pap_Features_PerformanceRewards_ActionList::getInstance(true);
        $list->addAction('a1', 'Pap_Features_PerformanceRewards_Action_AddBonusCommAction', self::ACTION_PUT_ADD_BONUS);
        $list->addAction('a2', 'Pap_Features_PerformanceRewards_Action_ChangeGroup', self::ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP);
        $list->addAction('a3', 'Pap_Features_PerformanceRewards_Action_ChangeGroupRetroactively', self::ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP_RETROACTIVELY);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Features.PerformanceRewards.Action.createActionList', $list);
    }
    
    /**
     *
     * @param Pap_Features_PerformanceRewards_Rule $rule
     * @return Pap_Features_PerformanceRewards_Action
     */
    public static function create(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        if (count(Pap_Features_PerformanceRewards_ActionList::getInstance()->getActionList()) == 0) {
            self::createActionsList();
        }
        $className = Pap_Features_PerformanceRewards_ActionList::getInstance()->getClassName($rule->getAction());
        return new $className($rule);
    }
    
    /**
     *
     * @param Pap_Features_PerformanceRewards_Rule $rule
     * @return Pap_Features_PerformanceRewards_Action
     */
    public static function toString($code) {
        return Pap_Features_PerformanceRewards_ActionList::getInstance()->toString($code);
    }
    
    public static function  getAllActions() {
        if (count(Pap_Features_PerformanceRewards_ActionList::getInstance()->getActionList()) == 0) {
            self::createActionsList();
        }
        return Pap_Features_PerformanceRewards_ActionList::getInstance()->getAllActions();
    }
    
    public abstract function getString();
    
    /**
     * @throws Gpf_Exception
     */
    public abstract function execute();
    
    protected function logMessage($message) {
        Gpf_Log::debug($message);
    }
    
    protected function getCurrentUserId() {
        return $this->rule->getCondition()->getCurrentUserId();
    }
}
?>
