<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Lang_AddLanguageGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Languages::CODE, $this->_("Language code"), false);
        $this->addViewColumn(Gpf_Db_Table_Languages::NAME, $this->_("Name"), false);
        $this->addViewColumn(Gpf_Db_Table_Languages::ENGLISH_NAME, $this->_("English name"), false);
        $this->addViewColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, $this->_("Translated [%]"), false);
        $this->addViewColumn(Gpf_Db_Table_Languages::AUTHOR, $this->_("Author"), false);
        $this->addViewColumn(Gpf_Db_Table_Languages::VERSION, $this->_("Version"), false);
        $this->addViewColumn(self::ACTIONS, $this->_("Import"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('id');
        $this->addDataColumn(Gpf_Db_Table_Languages::CODE, Gpf_Db_Table_Languages::CODE);
        $this->addDataColumn(Gpf_Db_Table_Languages::NAME, Gpf_Db_Table_Languages::NAME);
        $this->addDataColumn(Gpf_Db_Table_Languages::ENGLISH_NAME, Gpf_Db_Table_Languages::ENGLISH_NAME);
        $this->addDataColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE);
        $this->addDataColumn(Gpf_Db_Table_Languages::AUTHOR, Gpf_Db_Table_Languages::AUTHOR);
        $this->addDataColumn(Gpf_Db_Table_Languages::VERSION, Gpf_Db_Table_Languages::VERSION);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::CODE, '40px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::NAME, '60px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::ENGLISH_NAME, '60px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::AUTHOR, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Languages::VERSION, '40px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
    }

    /**
     * Returns row data for grid
     *
     * @service language read
     *
     * @param $filters
     * @param $limit
     * @param $offset
     * @param $sort_col
     * @param $sort_asc
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $this->_params = $params;
        $this->_sortColumn = $params->get('sort_col');
        $this->_sortAsc = $params->get('sort_asc');
        $this->_requiredColumns = new Gpf_Data_IndexedRecordSet('id');
        $this->_requiredColumns->loadFromArray($params->get('columns'));

        $response = new Gpf_Rpc_Object();


        $recordHeader = new Gpf_Data_RecordHeader();
        $result = new Gpf_Data_RecordSet();
        foreach ($this->getAllViewColumns() as $column) {
            $result->addColumn($column->get('id'));
            $recordHeader->add($column->get('id'));
        }
        $result->addColumn('id');
        $recordHeader->add('id');

        $languagesIterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getLanguageInstallDirectory(), 'csv', false);
        $languagesCount = 0;
        foreach ($languagesIterator as $fullName => $file) {
            if(preg_match('/^'.Gpf_Application::getInstance()->getCode().'_(.+)\.csv$/', $file, $matches)) {
                $file = new Gpf_Io_Csv_Reader($fullName, ';', '"',
                array('source','translation','type','module','status','customer'));
                $language = new Gpf_Lang_CsvLanguage();
                $language->loadFromCsvFile($file, true);

                $languagesCount++;
                $record = new Gpf_Data_Record($recordHeader);
                $record->set('id', $fullName);
                $record->set(Gpf_Db_Table_Languages::CODE, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_CODE));
                $record->set(Gpf_Db_Table_Languages::NAME, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_NAME));
                $record->set(Gpf_Db_Table_Languages::ENGLISH_NAME, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_ENG_NAME));
                $record->set(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_TRANSLATION_PERCENTAGE));
                $record->set(Gpf_Db_Table_Languages::AUTHOR, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_AUTHOR));
                $record->set(Gpf_Db_Table_Languages::VERSION, $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_VERSION));
                $result->addRecord($record);

            }
        }

        $response->rows = $result->toObject();
        $response->count = $languagesCount;
        return $response;
    }

    /**
     * Import language specified in parameter fileName
     *
     * @service language import
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function importLanguage(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        //Read metadata of file
        $file = new Gpf_Io_Csv_Reader($form->getFieldValue("fileName"), ';', '"',
        array('source','translation','type','module','status','customer'));
        $file->setMaxLinesToRead(10);
        $language = new Gpf_Lang_CsvLanguage();
        $language->loadFromCsvFile($file);
        $fileName = $form->getFieldValue("fileName");
        try {
            $importer = new Gpf_Lang_ImportLanguageTask($fileName, $language->getCode(), false);
            $importer->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $form->addField('progress', 'PROGRESS');
            $form->setFieldError('progress', $e->getMessage());
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        if(!@chmod($fileName, 0777)) {
            throw new Gpf_Exception($this->_('Import was successful, but if you want to change translations, please set permissions of file %s to 0777', $fileName));
        }

        $form->setInfoMessage($this->_('%s (%s) imported',
        $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_NAME),
        $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_ENG_NAME)));
        return $form;
    }


    /**
     * Load metadata from imported language
     *
     * @service language import
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function loadCsvFileMetadata(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            $file = new Gpf_Io_Csv_Reader($form->getFieldValue("Id"), ';', '"',
            array('source','translation','type','module','status','customer'));
            $language = new Gpf_Lang_CsvLanguage();
            $language->loadFromCsvFile($file);

            $form->setField('code', $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_CODE));
            $form->setField('name', $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_NAME));
            $form->setField('eng_name', $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_ENG_NAME));
            $form->setField('author', $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_AUTHOR));
            $form->setField('version', $language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_VERSION));
            $form->setField('translated', $language->getTranslationPercentage());
        } catch (Exception $e) {
            $form->setErrorMessage(
            $this->_('Failed to import language file. Incorrect file format. (%s)',
            $e->getMessage()));
        }
        return $form;
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
