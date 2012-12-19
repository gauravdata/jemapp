<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: PayoutOptionsGrid.class.php 25792 2009-10-23 11:28:08Z mjancovic $
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
class Pap_Merchants_Payout_PayoutOptionsGrid extends Gpf_View_GridService {

	protected function initViewColumns() {
		$this->addViewColumn('name', $this->_("Name"), true);
		$this->addViewColumn('rstatus', $this->_("Status"), true);
		$this->addViewColumn('rorder', $this->_("Order"), true);
		$this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn(Gpf_Db_Table_FieldGroups::ID);
		$this->addDataColumn('name', Gpf_Db_Table_FieldGroups::NAME);
		$this->addDataColumn('rstatus', Gpf_Db_Table_FieldGroups::STATUS);
		$this->addDataColumn('rorder', Gpf_Db_Table_FieldGroups::ORDER);
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn('name', '40px', 'N');
		$this->addDefaultViewColumn('rstatus', '40px', 'N');
		$this->addDefaultViewColumn('rorder', '40px', 'A');
		$this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
	}

	protected function buildFrom() {
		$this->_selectBuilder->from->add(Gpf_Db_Table_FieldGroups::getName());
	}

	protected function buildWhere() {
		parent::buildWhere();
		$this->_selectBuilder->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
		$this->_selectBuilder->where->add(Gpf_Db_Table_FieldGroups::TYPE, '=', Pap_Common_Constants::FIELDGROUP_TYPE_PAYOUTOPTION);
	}
	
	/**
     * @return Gpf_DbEngine_Row
     */
    protected function createEmptyRow() {
        $row = new Pap_Db_PayoutOption();
        $row->set(Gpf_Db_Table_Accounts::ID, Gpf_Application::getInstance()->getAccountId());   
        $row->setName($this->_("New payout option"));  
        $row->setStatus(Pap_Db_PayoutOption::DISABLED);
        $row->setOrder(1);
        
        $i = 2;
        while ($i < 10) {
            try {
                $row->check();
                break;
            } catch (Gpf_DbEngine_Row_CheckException $e) {
                $row->setName($this->_("New payout option %s", $i));
                $i++;
            }
        }
        
        return $row;
    }
    
    /**
     * @service payout_option read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service payout_option add
     * @return Gpf_Rpc_Serializable
     */
    public function getRowsAddNew(Gpf_Rpc_Params $params) {
        return parent::getRowsAddNew($params);
    }
    
    /**
     * @service payout_option export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
