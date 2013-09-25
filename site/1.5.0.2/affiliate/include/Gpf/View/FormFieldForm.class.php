<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormFieldForm.class.php 21460 2008-10-09 07:04:12Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_View_FormFieldForm extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_FormField();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Field");
    }
       
    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->set(Gpf_Db_Table_Accounts::ID, Gpf_Session::getAuthUser()->getAccountId());
    }
    
    /**
     * @service form_field read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }
    
    /**
     * @service form_field add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     * @service form_field write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     * @service form_field write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }
    
     /**
     * @service form_field delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
