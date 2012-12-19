<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 20390 2008-08-29 13:11:12Z mbebjak $
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
class Pap_Merchants_Config_MerchantForm extends Gpf_View_FormService {
	
	const WELCOME_MESSAGE = 'WelcomeMessage';

    public function __construct() {
    }
    
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Merchants_User();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Merchant");
    }
    
    protected function getId(Gpf_Rpc_Form $form) {
       return Gpf_Session::getAuthUser()->getPapUserId();
    }
    
    protected function checkBeforeSave($row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
        return true;
    }
       
    /**
     * @service merchant read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        $form->addField(self::WELCOME_MESSAGE, Gpf_Settings::get(Pap_Settings::WELCOME_MESSAGE));
        return $form;
    }

    /**
     *
     * @service merchant write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = parent::save($params);
        
        if ($form->existsField('WelcomeMessage')) {
            Gpf_Settings::set(Pap_Settings::WELCOME_MESSAGE, $form->getFieldValue("WelcomeMessage"));
        }
        return $form;
    }
    
    /**
     *
     * @service merchant write
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }
    
    /**
     * Loads list of fields for merchant dynamic form panel
     *
     * @anonym
     * @service
     */
    public function getFields(Gpf_Rpc_Params $params) {
        $merchantForm = new Pap_Merchants_Config_MerchantFormDefinition();
        $merchantForm->check();
        
        $formFields = Gpf_Db_Table_FormFields::getInstance();
        return $formFields->getFields($params);
    }
    
}

?>
