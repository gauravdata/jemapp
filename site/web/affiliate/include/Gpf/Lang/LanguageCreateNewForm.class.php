<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 20081 2008-08-22 10:21:35Z vzeman $
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
class Gpf_Lang_LanguageCreateNewForm extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Db_Language();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Language");
    }

    /**
     * Do nothing - form should be always empty
     * @service language read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception("Function not supported");
    }

    /**
     * Create new language
     *
     * @service language add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $lang = new Gpf_Db_Language();
        $lang->setCode($form->getFieldValue(Gpf_Db_Table_Languages::CODE));
        $lang->setAccountId(Gpf_Application::getInstance()->getAccountId());
        $lang->setId($lang->generateId());

        try {
            //Load should fail, otherwise it is error - language already exists
            $lang->load();
            $form->setErrorMessage(
            $this->_('Language code %s is already used in your installation',
            $form->getFieldValue(Gpf_Db_Table_Languages::CODE)));
            return $form;
        } catch (Exception $e) {
        }

        try {
            //Load language from default csv file
            $fileNameDefault =  Gpf_Paths::getInstance()->getLanguageInstallDirectory() .
            Gpf_Application::getInstance()->getCode() .
                '_' . Gpf_Lang_CsvHandler::DEFAULT_LANGUAGE . '.csv';

            $file = new Gpf_Io_Csv_Reader($fileNameDefault, ';', '"',
            array('source','translation','type','module','status','customer'));
            $csvLanguage = new Gpf_Lang_CsvLanguage();
            $csvLanguage->loadFromCsvFile($file);

            $form->fill($lang);
            $lang->setAccountId(Gpf_Application::getInstance()->getAccountId());
            $lang->setId($lang->generateId());
            $lang->setActive(true);
            $lang->setTranslatedPercentage(0);
            $lang->insert();

            //update metadata
            $csvLanguage->copyMetadataFromDbLanguage($lang);

            foreach ($csvLanguage->getTranslations() as $translation) {
                $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_NOT_TRANSLATED);
            }

            //export cache
            $csvLanguage->exportAccountCache();

        } catch (Exception $e) {
            $form->setErrorMessage($this->_('Failed to create new language: %s', $e->getMessage()));
            return $form;
        }

        $form->setInfoMessage($this->_('New language with code %s created', $form->getFieldValue(Gpf_Db_Table_Languages::CODE)));
        return $form;
    }

    /**
     * @service language write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        return self::add($params);
    }

    /**
     * @service language write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service language delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
