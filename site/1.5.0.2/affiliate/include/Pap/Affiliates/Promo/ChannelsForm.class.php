<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ChannelsForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Affiliates_Promo_ChannelsForm extends Gpf_View_FormService {
    
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Db_Channel();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Channel");
    }
    
    /**
     * @service channel write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to save %s field(s)'));
        $action->setInfoMessage($this->_('%s field(s) successfully saved'));
        
        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));
        
        $channels = Pap_Db_Table_Channels::getUserChannels(Gpf_Session::getAuthUser()->getPapUserId());
        
        foreach ($fields as $field) {
        	$isUnique = true;
        	foreach ($channels as $channel) {
        		if ($field->get('value') == $channel->get(Pap_Db_Table_Channels::VALUE)) {
        			$isUnique = false;
        			break;
        		}
        	}
        	if ($isUnique) {
        		$this->saveField($field);
        	} else {
        		$action->setErrorMessage($this->_("Failed to save %s field(s). Data with value '".$field->get('value').
        		  "' already exist. Data must be unique."));
        		$action->addError();
        		return $action;
        	}
        }

        $action->addOk();

        return $action;
    }
    
    /**
     * @service channel delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
    
    private function saveField(Gpf_Data_Record $field) {
    	$dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($field->get('id'));
        $dbRow->load();
        $dbRow->set($field->get("name"), $field->get("value"));
        $dbRow->save();
    }
}

?>
