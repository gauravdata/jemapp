<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_ActionCommission_CommissionsForm extends Gpf_Object {
    
    private static $instance = false;

    /**
     * @return Pap_Features_SplitCommissions_SplitCommissionsForm
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_ActionCommission_CommissionsForm();
        }
        return self::$instance;
    }
    
    public function save(Pap_Merchants_Campaign_CommissionTypeRpcForm $form) {
        if($form->getFieldValue('rtype') != Pap_Common_Constants::TYPE_ACTION) {
            return;
        }
        $update =  new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_CommissionTypes::NAME, $form->getFieldValue(Pap_Db_Table_CommissionTypes::NAME));
        $update->from->add(Pap_Db_Table_CommissionTypes::getName());
        $update->where->add(Pap_Db_Table_CommissionTypes::CODE, '=', $form->getFieldValue(Pap_Db_Table_CommissionTypes::CODE));
        $update->execute();
    }
}

?>
