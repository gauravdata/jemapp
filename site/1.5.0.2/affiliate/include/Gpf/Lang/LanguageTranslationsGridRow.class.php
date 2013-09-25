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
class Gpf_Lang_LanguageTranslationsGridRow extends Gpf_View_FormService {

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Gpf_Lang_Parser_Translation();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Translation");
    }

    /**
     * @service language read
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service language add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $translation = new Gpf_Lang_Parser_Translation();
        $languageId = '';

        //load fields to translation and languageId
        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($params->get("fields"));
        foreach ($fields as $field) {
            switch ($field->get('name')) {
                case 'translation':
                    $translation->setDestinationMessage($field->get("value"));
                    break;
                case 'source':
                    $translation->setSourceMessage($field->get("value"));
                    break;
                case 'languageid':
                    $languageId = $field->get("value");
                default:
                    break;
            }
        }

        $language = new Gpf_Db_Language();
        $language->setId($languageId);
        $language->load();

        $csvFile = new Gpf_Io_Csv_Reader(Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode()));

        $csvLanguage = new Gpf_Lang_CsvLanguage();
        $csvLanguage->loadFromCsvFile($csvFile);

        if ($csvLanguage->existTranslation($translation)) {
            $form->setErrorMessage($this->_('Translation already exist! You can not add source message multiple times.'));
            return $form;
        } else {
            $translation->setCustomerSpecific(true);
            $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_TRANSLATED);
            $translation->setType(Gpf_Lang_Parser_Translation::TYPE_BOTH);
            $csvLanguage->addTranslation($translation);
        }

        $csvLanguage->exportAccountCache();

        $language->setTranslatedPercentage($csvLanguage->getTranslationPercentage());
        $language->save();

        $form->setInfoMessage($this->_("%s was successfuly added", $this->_('translation')));

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
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to save %s field(s)'));
        $action->setInfoMessage($this->_('%s field(s) successfully saved'));
        
        $language = new Gpf_Db_Language();
        $language->setId($action->getParam(Gpf_Db_Table_Languages::ID));
        $language->load();

        $csvFile = new Gpf_Io_Csv_Reader(Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode()));


        $csvLanguage = new Gpf_Lang_CsvLanguage();
        $csvLanguage->loadFromCsvFile($csvFile);


        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));


        foreach ($fields as $field) {
            $translation = new Gpf_Lang_Parser_Translation();
            $translation->setSourceMessage($field->get('id'));
            if ($csvLanguage->existTranslation($translation)) {
                $existingTranslation = $csvLanguage->getTranslation($translation);
                if ($existingTranslation->getStatus() == Gpf_Lang_Parser_Translation::STATUS_NOT_TRANSLATED) {
                    $existingTranslation->setStatus(Gpf_Lang_Parser_Translation::STATUS_TRANSLATED);
                    $csvLanguage->incrementTranslatedCount();
                }
                $existingTranslation->set($field->get("name"), $this->sourceCodeSpecialChars($field->get("value")));
            }

            $action->addOk();
        }
        $csvLanguage->exportAccountCache();
        $language->setTranslatedPercentage($csvLanguage->getTranslationPercentage());
        $language->save();
        return $action;
    }

    /**
     * @service language delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception('Not implemented');
    }

    private function sourceCodeSpecialChars($value) {
        $value = str_replace('<?', '&lt;?', $value);
        return str_replace('?>', '?&gt;', $value);
    }
}

?>
