<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm extends Gpf_Plugins_Config {
   
    private static $instance;
    
    /**
     * @return Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initFields() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeEditAdditionalForm.initFields', $this);
    }

    /**
     * @anonym     
     * @service
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeEditAdditionalForm.save', $form);
        
        return $form;
    }

    /**
     * @anonym
     * @service
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeEditAdditionalForm.load', $form);
        
        return $form;
    }
    
}
?>
