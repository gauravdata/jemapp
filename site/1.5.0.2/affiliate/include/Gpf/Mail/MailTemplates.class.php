<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailTemplates.class.php 22049 2008-11-01 13:31:20Z aharsani $
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
class Gpf_Mail_MailTemplates extends Gpf_View_GridService {
    
    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_MailTemplates::TEMPLATE_NAME, $this->_("Name"), true);
    }
    
    protected function initDataColumns() {
    	$this->setKeyDataColumn(Gpf_Db_Table_MailTemplates::ID);
        $this->addDataColumn(Gpf_Db_Table_MailTemplates::TEMPLATE_NAME, Gpf_Db_Table_MailTemplates::TEMPLATE_NAME);
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_MailTemplates::TEMPLATE_NAME);
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_MailTemplates::getName());
    }
    
    /**
     * @service mail_template read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
       
    /**
     * @service mail_template export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
