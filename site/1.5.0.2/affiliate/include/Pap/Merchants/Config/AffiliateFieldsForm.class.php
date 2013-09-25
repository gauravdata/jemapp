<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: FormFieldForm.class.php 20176 2008-08-26 13:55:39Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Config_AffiliateFieldsForm extends Pap_Merchants_Config_ConfigFieldsForm {

    protected function getFormDefinition(){
        return  new Pap_Merchants_Config_AffiliateFormDefinition();
    }

    protected function getFormId(){
        return Pap_Merchants_Config_AffiliateFormDefinition::FORMID;
    }

    /**
     * @service form_field write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return $this->saveFieldsRpc($params);
    }

    /**
     * @service form_field read
     * @return Gpf_Rpc_Form
     */
    public function loadFieldsFromFormID(Gpf_Rpc_Params $params) {
        return $this->loadFieldsFromFormIDRpc($params);
    }

    /**
     * @service form_field write
     * @return Gpf_Rpc_Form
     */
    public function saveUsernameSetting(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES, $form->getFieldValue(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES));
        return $form;
    }

    /**
     * @service form_field read
     * @return Gpf_Rpc_Form
     */
    public function loadUsernameSetting(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES, Gpf_Settings::get(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES));
        return $form;
    }

    /**
     * @service form_field write
     * @return Gpf_Rpc_Form
     */
    public function saveParentAffiliateSetting(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(Pap_Settings::NOT_SET_PARENT_AFFILIATE, $form->getFieldValue(Pap_Settings::NOT_SET_PARENT_AFFILIATE));
        return $form;
    }

    /**
     * @service form_field read
     * @return Gpf_Rpc_Form
     */
    public function loadParentAffiliateSetting(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(Pap_Settings::NOT_SET_PARENT_AFFILIATE, Gpf_Settings::get(Pap_Settings::NOT_SET_PARENT_AFFILIATE));
        return $form;
    }
}

?>
