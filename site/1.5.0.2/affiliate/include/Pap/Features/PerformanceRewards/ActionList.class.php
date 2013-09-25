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
class Pap_Features_PerformanceRewards_ActionList extends Gpf_Object {

    protected $actionList;
    protected static $instance = null;
    
    /**
    * @return Pap_Features_PerformanceRewards_ActionList
     */
    public static function getInstance($clear = false) {
        if((self::$instance === null)||($clear == true)) {
            self::$instance = new Pap_Features_PerformanceRewards_ActionList();
        }
        return self::$instance;
    }
    
    public function addAction($name, $className, $actionCode) {
        $this->getInstance()->actionList[$actionCode] = array('name'=>$name, 'className'=>$className);
    }
                       
    /**
     *
     * @param Pap_Features_PerformanceRewards_Rule $rule
     * @return Pap_Features_PerformanceRewards_Action
     */
    public function toString($code) {
        $className = self::getClassName($code);
        if (is_callable(array($className, 'toString'))) {
            return call_user_func(array($className, 'toString'));
        }
        throw new Gpf_Exception('Action ' . $className . ' has no description.');
    }
    
    /**
     *
     * @param unknown_type $code
     * @return string
     */
    public function getClassName($code) {
        if (array_key_exists($code, $this->getActionList())) {
            return $this->actionList[$code]['className'];
        }
        throw new Pap_Features_PerformanceRewards_UnknownRuleException('Unsupported action with code ' . $code);
    }
    
    public function getActionList() {
        return $this->actionList;
    }
    
    public function  getAllActions() {
        $list = array();
        foreach ($this->actionList as $key => $action) {
            $list[] = $key;
        }
        return $list;
    }
}
?>
