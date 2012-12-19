<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Dictionary.class.php 19083 2008-07-10 16:32:14Z aharsani $
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
class Gpf_Lang_LanguagesGrid extends Gpf_View_GridService {


    protected function initViewColumns() {
        $this->addViewColumn("fullname", $this->_("Language Name"), true);
        $this->addViewColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, $this->_("Translated [%]"), true);
        $this->addViewColumn(Gpf_Db_Table_Languages::IS_DEFAULT, $this->_("Is default"), true);
        $this->addViewColumn(Gpf_Db_Table_Languages::ACTIVE, $this->_("Is active"), true);
        $this->addViewColumn(Gpf_Db_Table_Languages::IMPORTED, $this->_("Imported"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(self::ACTIONS, $this->_('Actions'), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Languages::ID);
        $this->addDataColumn(Gpf_Db_Table_Languages::ID, Gpf_Db_Table_Languages::ID);
        $this->addDataColumn(Gpf_Db_Table_Languages::CODE, Gpf_Db_Table_Languages::CODE);
        $this->addDataColumn(Gpf_Db_Table_Languages::NAME, Gpf_Db_Table_Languages::NAME);
        $this->addDataColumn(Gpf_Db_Table_Languages::ENGLISH_NAME, Gpf_Db_Table_Languages::ENGLISH_NAME);
        $this->addDataColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE);
        $this->addDataColumn(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf_Db_Table_Languages::IS_DEFAULT);
        $this->addDataColumn(Gpf_Db_Table_Languages::ACTIVE, Gpf_Db_Table_Languages::ACTIVE);
        $this->addDataColumn(Gpf_Db_Table_Languages::IMPORTED, Gpf_Db_Table_Languages::IMPORTED);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("fullname", '80px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::IS_DEFAULT, '30px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::ACTIVE, '30px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::IMPORTED, '70px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Languages::getName());
    }
    
    /**
     * Download cached csv language file
     *
     * @service language export
     * @param Gpf_Rpc_Params $params
     */
    public function downloadCsvLanguage(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);                        
        $download = new Gpf_File_Download_FileSystem(
        Gpf_Lang_CsvLanguage::getAccountCsvFileName($form->getFieldValue('code')));
        $download->setAttachment(true);
        return $download;
    }
    
    
    /**
     * Set language as default and unset another default language
     *
     * @service language write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function setLanguageAsDefault(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            Gpf_Db_Table_Languages::getInstance()->unsetDefaultLanguage($action->getParam(Gpf_Db_Table_Languages::ID));
            
            $lang = new Gpf_Db_Language();
            $lang->setId($action->getParam(Gpf_Db_Table_Languages::ID));
            $lang->load();
            $lang->setIsDefault(true);
            $lang->save();
        } catch (Exception $e) {
            $action->addError();
            $action->setErrorMessage($this->_('Failed to set default language with error: %s', $e->getMessage()));
            return $action;
        }

        $action->setInfoMessage($this->_('Language %s set as default.', $lang->getEnglishName()));
        $action->addOk();
        return $action;
    }
    
    /**
     * @service language read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
       
    /**
     * @service language export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
