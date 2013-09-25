<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: SettingsExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_SettingsImportExport extends Gpf_Csv_ObjectImportExport {

    public function __construct() {
    	parent::__construct();
        $this->setName(Gpf_Lang::_runtime('Settings'));
        $this->setDescription(Gpf_Lang::_runtime("SettingsImportExportDescription"));
    }

    protected function writeData() {
        $this->writeSelectBuilder($this->getSettingsFromDB());
        $this->writeFileData();
    }

    protected function deleteData() {
        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Gpf_Db_Table_Settings::getName());
        $deleteBuilder->execute();
    }

    protected function readData() {
        $this->readDbRow($this->getArrayHeaderColumns($this->getSettingsFromDB()));
    }

    protected function checkData() {
        $this->checkFile($this->getArrayHeaderColumns($this->getSettingsFromDB()));
        $this->rewindFile();
    }

    private function getSettingsFromDB() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Gpf_Db_Table_Settings::NAME, Gpf_Db_Table_Settings::NAME);
        $selectBuilder->select->add(Gpf_Db_Table_Settings::VALUE, Gpf_Db_Table_Settings::VALUE);
        $selectBuilder->from->add(Gpf_Db_Table_Settings::getName());

        return $selectBuilder;
    }
     
    private function writeFileData() {
        $accountId = Gpf_Session::getAuthUser()->getAccountId();
        $settings = new Gpf_File_Settings($accountId);

        foreach ($settings->getAll() as $name => $value) {
            $this->file->writeArray(array($name, $value));
        }
    }

    public function readDbRow(array $columns) {
        if ($this->findCode()) {
            if ($headerArray = $this->file->readArray()) {
                if ($this->hasData($headerArray)) {
                    if (isset($this->paramsArray['importedLinesCount'])) {
                        $this->importedLinesCount = $this->paramsArray['importedLinesCount'];
                    }
                    if (isset($this->paramsArray['wrongLinesCount'])) {
                        $this->wrongLinesCount = $this->paramsArray['wrongLinesCount'];
                    }
                    $headerColumns = $this->mapHeaderColumns($columns, $headerArray);
                    $headerColumns = $this->removeAlias($headerColumns);
                    $this->importRows($headerColumns, $headerArray);
                }
            }
        }
    }

    protected function importRows(array $columns, array $headerArray) {
        while ($row = $this->file->readArray()) {
            $this->logger->debug('Reading row ('.$this->file->getActualCSVLineNumber().'): ' . implode($this->delimiter, $row));
            $this->paramsArray['importedLinesCount'] = $this->importedLinesCount;
            $this->paramsArray['wrongLinesCount'] = $this->wrongLinesCount;
            if ($this->isPending(implode($this->delimiter, $row), $this->_('Import rows in %s', $this->getName()))) {
                if ($this->hasData($row)) {
                    if (count($row) >= count($headerArray)) {
                        $setting = array();
                        for ($i = 0; $i < count($headerArray); $i++) {
                            if (array_key_exists($headerArray[$i], $columns)) {
                                $setting[$columns[$headerArray[$i]]] = $row[$i];
                            }
                        }
                        if (count($setting) == 2) {
                            try {
                                Gpf_Settings::set($setting[Gpf_Db_Table_Settings::NAME], $setting[Gpf_Db_Table_Settings::VALUE]);
                                $this->logger->debug('Data from line: ' . $this->file->getActualCSVLineNumber() . ' was inserted.');
                                $this->incrementSuccessfulCount();
                            } catch (Gpf_Exception $e) {
                            	$this->logError($this->getName(), $row, $e);
                            	$this->appendOutputMessage($this->getSaveErrorMessage($this->file->getActualCSVLineNumber(), $e->getMessage()));
                            	$this->incrementWrongCount();
                            }
                        } else {
                            $this->logger->debug('Wrong settings data count (line ' . $this->file->getActualCSVLineNumber() . ').');
                            $this->appendOutputMessage($this->getLessItemsMessage($this->file->getActualCSVLineNumber()));
                            $this->incrementWrongCount();
                        }
                    } else {
                        $this->logger->debug($this->getLessItemsMessage($this->file->getActualCSVLineNumber(), false));
                        $this->appendOutputMessage($this->getLessItemsMessage($this->file->getActualCSVLineNumber()));
                        $this->incrementWrongCount();
                    }
                } else {
                    $this->logger->debug($this->getEndDataMessage($this->file->getActualCSVLineNumber(), ($this->getPartName() != null ? $this->getPartName() : $this->getName())));
                    $this->setDone();
                    break;
                }
                $this->setDone();
            }
        }
        $this->logger->debug($this->getEndFileMessage($this->getPartName() != null ? $this->getPartName() : $this->getName()));
        $this->appendOutputData($this->importedLinesCount, $this->wrongLinesCount);
        $this->resetImportedCounts();
    }
}
?>
