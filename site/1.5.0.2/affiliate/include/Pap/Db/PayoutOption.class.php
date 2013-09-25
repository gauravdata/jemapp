<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: PayoutOption.class.php 28715 2010-06-30 13:34:46Z iivanco $
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
class Pap_Db_PayoutOption extends Gpf_Db_FieldGroup {

    function __construct(){
        parent::__construct();
        $this->setType(Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);
    }
    
    public function setExportHeaderTemplate($template) {
        $this->set(Gpf_Db_Table_FieldGroups::DATA1, $template);
    }
    
    public function getExportHeaderTemplate() {
        return $this->get(Gpf_Db_Table_FieldGroups::DATA1);
    }
    
    public function setExportRowTemplate($template) {
        $this->set(Gpf_Db_Table_FieldGroups::DATA2, $template);
    }
    
    public function getExportRowTemplate() {
        return $this->get(Gpf_Db_Table_FieldGroups::DATA2);
    }
    
    public function setExportFooterTemplate($template) {
        $this->set(Gpf_Db_Table_FieldGroups::DATA3, $template);
    }
    
    public function getExportFooterTemplate() {
        return $this->get(Gpf_Db_Table_FieldGroups::DATA3);
    }
    
    public function setExportFileName($fileName) {
        $this->set(Gpf_Db_Table_FieldGroups::DATA4, $fileName);
    }
    
    public function getExportFileName() {
        return $this->get(Gpf_Db_Table_FieldGroups::DATA4);
    }
    
    public function getFormId() {
        return 'payout_option_'.$this->getID();
    }
    
    /**
     * Gets payout option names for PayoutSearchListBox
     *
     * @anonym
     * @service payout_option read
     * @param $search
     */
    public function getPayouts(Gpf_Rpc_Params $params) {
        $searchString = $params->get('search');
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_FieldGroups::ID, "id");
        $select->select->add(Gpf_Db_Table_FieldGroups::NAME, "name");
        $select->from->add(Gpf_Db_Table_FieldGroups::getName());
        $select->where->add(Gpf_Db_Table_FieldGroups::NAME, "LIKE", "%".$searchString."%");
    	$select->where->add(Gpf_Db_Table_FieldGroups::TYPE, '=', Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);
    	$select->where->add(Gpf_Db_Table_FieldGroups::STATUS, '=', Gpf_Db_FieldGroup::ENABLED);
        $select->orderBy->add(Gpf_Db_Table_FieldGroups::ORDER);
        
        $result = new Gpf_Data_RecordSet();
        $result->load($select);
        
        foreach ($result as $record) {
            $record->set("name", $this->_localize($record->get("name")));
        }
        
        return $result;
    }
        
    public static function getAllPayoutOptionNames() {
    	$result = new Gpf_Data_RecordSet('id');

    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
    	$selectBuilder->select->add(Gpf_Db_Table_FieldGroups::ID, 'id');
    	$selectBuilder->select->add(Gpf_Db_Table_FieldGroups::NAME, 'name');
    	$selectBuilder->from->add(Gpf_Db_Table_FieldGroups::getName());
    	$selectBuilder->where->add(Gpf_Db_Table_FieldGroups::TYPE, '=', Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);

    	$result->load($selectBuilder);
    	
    	$resultArray = array();
    	foreach($result as $record) {
    		$resultArray[$record->get('id')] = Gpf_Lang::_localizeRuntime($record->get('name'));
    	}
    	return $resultArray;
    }
    
    /**
     * Gets payout option name for Default payout method
     *
     * @service
     * @anonym
     * @param $id
     */
    public function getPayoutName(Gpf_Rpc_Params $params) {
    	$data = new Gpf_Rpc_Data($params);
        $payoutOption = new Gpf_Db_FieldGroup();
        $this->setType(Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);
        $payoutOption->setPrimaryKeyValue($params->get('id'));
        try {
        	$payoutOption->load();
        	$data->setValue('payoutoptionid', $payoutOption->getPrimaryKeyValue());
        	$data->setValue('name', $this->_localize($payoutOption->getName()));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $data->setValue('payoutoptionid', null);
        	$data->setValue('name', $this->_('None'));
        }
        
        return $data;
    }
}

?>
