<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Merchants_Campaign_Commissions extends Gpf_Object {
	/**
	 * returns default commission group for a campaign
	 *
	 * @service commission read
	 * @param $fields
	 * @return Gpf_Rpc_Form
	 */
	public function loadDefaultCommissionGroup(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		$commissionGroupId = $this->getDefaultCommissionGroupId($form->getFieldValue("campaignid"));
		$form->setField("commissiongroupid", $commissionGroupId);

		return $form;
	}

	/**
	 * returns commission types for a campaign
	 *
	 * @service commission read
	 * @param $fields
	 */
	public function loadCommissionTypes(Gpf_Rpc_Params $params) {
		$campaignId = $params->get("campaignid");
		if($campaignId == "") {
			throw new Exception($this->_("Campaign ID cannot be empty!"));
		}

		return Pap_Db_Table_CommissionTypes::getInstance()->getAllCommissionTypes($campaignId);
	}

	/**
	 * returns all commission types for a campaign
	 *
	 * @service commission read
	 * @param $fields
	 */
	public function loadAllCommissionSettings(Gpf_Rpc_Params $params) {
		$commissionGroupId = $params->get("commissiongroupid");
		$campaignId = $params->get("campaignid");
		$rtype = $params->get("rtype");
		if($campaignId != '') {
			if($commissionGroupId == '') {
				$commissionGroupId = $this->getDefaultCommissionGroupId($campaignId);
			}
			return Pap_Db_Table_Commissions::getInstance()->getAllCommissions('', $commissionGroupId);
		}
		if($rtype != '') {
			return Pap_Db_Table_Commissions::getInstance()->getAllCommissions('', $commissionGroupId);
		}
		throw new Exception($this->_("Campaign ID cannot be empty!"));
	}

	/**
	 * changes status for commissiontype record
	 *
	 * @service commission write
	 * @param $fields
	 * @return Gpf_Rpc_Action
	 */
	public function changeCommissionTypeStatus(Gpf_Rpc_Params $params) {
		$action = new Gpf_Rpc_Action($params);
		$action->setErrorMessage($this->_('Failed to change status'));
		$action->setInfoMessage($this->_('Status successfully changed'));

		$commType = new Pap_Db_CommissionType();
		$commType->set(Pap_Db_Table_CommissionTypes::ID, $action->getParam('commtypeid'));
		$commType->load();

		$commType->set(Pap_Db_Table_CommissionTypes::STATUS, $action->getParam('rstatus'));

		$commType->save();

		if ($action->getParam('rstatus')==Pap_Common_Constants::ESTATUS_DISABLED) {
			$this->changeCommissionTypeChildsStatus($action->getParam('commtypeid'), $action->getParam('rstatus'));
		}

		$action->addOk();
		return $action;
	}

	/**
	 *
	 * @service commission write
	 * @param $fields
	 * @return Gpf_Rpc_Action
	 */
	public function deleteCommissionType(Gpf_Rpc_Params $params) {
		$action = new Gpf_Rpc_Action($params);
		$action->setErrorMessage($this->_('Failed to delete type, some transactions are connected to this type. Remove them first.'));
		$action->setInfoMessage($this->_('Commission type succesfully removed'));

		$commTypeId = $action->getParam('commtypeid');

		$transaction = new Pap_Db_Transaction();
		$transaction->setCommissionTypeId($commTypeId);
		$collection = $transaction->loadCollection(array(Pap_Db_Table_Transactions::COMMISSIONTYPEID));
		if ($collection->getSize() > 0) {
			$action->addError();
			return $action;
		}

		try {
			$select = new Gpf_SqlBuilder_DeleteBuilder();
			$select->from->add(Pap_Db_Table_CommissionTypes::getName());
			$select->where->add(Pap_Db_Table_CommissionTypes::ID, '=', $commTypeId);
			$select->execute();
		} catch (Gpf_Exception $e) {
			$action->setErrorMessage($this->_('Error during removing commission type:' . $e->getMessage()));
			$action->addError();
			return $action;
		}

		try {
			$select = new Gpf_SqlBuilder_DeleteBuilder();
			$select->from->add(Pap_Db_Table_Commissions::getName());
			$select->where->add(Pap_Db_Table_Commissions::TYPE_ID, '=', $commTypeId);
			$select->execute();
		} catch (Gpf_Exception $e) {
			$action->setErrorMessage($this->_('Error during removing commission type settings:' . $e->getMessage()));
			$action->addError();
			return $action;
		}

		$action->addOk();
		return $action;
	}

	private function changeCommissionTypeChildsStatus($commtypeId, $status) {
		$update = new Gpf_SqlBuilder_UpdateBuilder();
		$update->from->add(Pap_Db_Table_CommissionTypes::getName(), 't');
		$update->set->add('t.'.Pap_Db_Table_CommissionTypes::STATUS, $status);
		$update->where->add('t.'.Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', $commtypeId);
		$update->execute();
	}

	/**
	 * Returns default commission group in campaign.
	 * If it doesn't exist, function will create & return
	 * new default commission group record.
	 * @param $campaignId
	 */
	public function getDefaultCommissionGroupId($campaignId) {
		if ($campaignId == "") {
			throw new Exception($this->_("Campaign ID cannot be empty!"));
		}
		$campaign = new Pap_Common_Campaign();
		$campaign->setId($campaignId);
		return $campaign->getDefaultCommissionGroup();
	}
}
?>
