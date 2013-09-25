<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CustomersGridForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Features_CommissionGroups_CommissionGroupsForm extends Gpf_View_FormService {
    
    /**
     * @return Pap_Db_CommissionGroup
     */
    protected function createDbRowObject() {
        return new Pap_Db_CommissionGroup();
    }
    
    /**
     * @return String
     */
    protected function getDbRowObjectName() {
        return $this->_("Commission group");
    }
    
    /** 
     *
     * @service commission_group add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }
    
    /**
     *
     * @service commission_group write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
    
    /**
     *
     * @service commission_group read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service commission_group delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {        
        return parent::deleteRows($params);
    }
}

?>
