<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
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
class Pap_Merchants_Config_AffiliatePanel extends Gpf_View_FormService {

    /**
     * @return Pap_Db_AffiliateScreen
     */
    protected function createDbRowObject() {
        return new Pap_Db_AffiliateScreen();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Affiliate screen");
    }

    /**
     * @param Pap_Db_AffiliateScreen $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $dbRow->setShowHeader(Pap_Db_AffiliateScreen::HEADER_SHOW);
    }


    /**
     * @service affiliate_panel_settings read
     * @param $fields
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $data->setValue(Pap_Settings::AFFILIATE_MENU, Gpf_Settings::get(Pap_Settings::AFFILIATE_MENU));

        return $data;
    }

    public function loadTreeNoRpc() {
        return $this->loadTree(new Gpf_Rpc_Params());
    }

    /**
     *
     * @service affiliate_panel_settings write
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveTree(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Affiliate panel menu saved"));

        try {
            Gpf_Settings::set(Pap_Settings::AFFILIATE_MENU, $action->getParam(Pap_Settings::AFFILIATE_MENU));
            $action->addOk();
        } catch (QUnit_Exception $e) {
            $action->setErrorMessage($e);
            $action->addError();
        }

        return $action;
    }

    /**
     *
     * @service affiliate_panel_settings add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = parent::add($params);

        if ($form->isSuccessful() && $form->getFieldValue("code") == "Custom-Page") {
            try {
            	$templatePaths = Gpf_Paths::getInstance()->getTemplateSearchPaths("affiliates", "", true);
            	$fileName = $templatePaths[0] . $form->getFieldValue("templateName").".tpl";
            	$file = new Gpf_Io_File($fileName);
            	$file->open('w');
            	$file->write($form->getFieldValue("templateName").".tpl");
            	$file->close();
            } catch (Exception $e) {
                $form->setErrorMessage($e->getMessage());
                return $form;
            }
        }
        return $form;
    }

    /**
     * @service affiliate_panel_settings read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service affiliate_panel_settings delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);

        $action->setInfoMessage($this->_('Affiliate screen successfully deleted'));

        foreach ($action->getIds() as $id) {
            try {
                $row = $this->createDbRowObject();
                $row->setPrimaryKeyValue($id);
                $row->delete();
                $action->addOk();
            } catch (Exception $e) {
                $action->setErrorMessage($e->getMessage());
                $action->addError();
            }
        }

        return $action;
    }

    /**
     * @service affiliate_panel_settings write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
}

?>
