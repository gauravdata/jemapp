<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: GadgetGrid.class.php 22049 2008-11-01 13:31:20Z aharsani $
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
class Gpf_Gadget_GadgetGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('url', $this->_("Url"), true);
        $this->addViewColumn('positiontype', $this->_("Docking"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Gadgets::ID);
        $this->addDataColumn('name', Gpf_Db_Table_Gadgets::NAME);
        $this->addDataColumn('url', Gpf_Db_Table_Gadgets::URL);
        $this->addDataColumn('positiontype', Gpf_Db_Table_Gadgets::POSITION_TYPE);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '40px', 'A');
        $this->addDefaultViewColumn('url', '40px', 'N');
        $this->addDefaultViewColumn('positiontype', '40px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Gadgets::getName());
    }
    
   protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add(Gpf_Db_Table_Gadgets::ACCOUNTUSERID,
            '=', Gpf_Session::getAuthUser()->getAccountUserId());
    }
    
   /**
     * @service gadget read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service gadget export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }  
}
?>
