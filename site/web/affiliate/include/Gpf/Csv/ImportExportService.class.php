<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExportService.class.php 19023 2008-07-08 12:50:59Z mfric $
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
class Gpf_Csv_ImportExportService extends Gpf_Object {

    private $delimiter;
    private $codes;
    private $date;
    private $note;

    private $outputMessage = '';

    const EXPORT_DIRECTORY = "export/";
    const SUCCESS = 'success';

    /**
     * Register object to import export
     *
     * @param String $className
     * @param String $accountId
     */
    public static function register(Gpf_Csv_ObjectImportExport $class, $accountId = null) {
        if($accountId === null) {
            $accountId = Gpf_Session::getAuthUser()->getAccountId();
        }
        $importExport = new Gpf_Db_ImportExport();
        $importExport->setName($class->getName());
        $importExport->setCode($class->getCode());
        $importExport->setDescription($class->getDescription());
        $importExport->setClassName(get_class($class));
        $importExport->setAccountId($accountId);
        $importExport->insert();
    }

    public static function getDelimiter($value) {
        switch ($value) {
            case "c":
                return ",";
                break;
            case "s":
                return ";";
                break;
            case "t":
                return "\t";
                break;
            default:
                return ",";
        }
    }

    /**
     * Generate csv file
     *
     * @service import_export export
     * @param $fields (classNames{className1,className2,..}, delimiter, note)
     */
    public function exportCSV(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $this->codes = $form->getFieldValue("codes");
        $this->delimiter = self::getDelimiter(($form->getFieldValue("delimiter")));
        $this->note = $form->getFieldValue("note");
        $date = date("Y-m-d_H-i-s");
        $this->date = str_replace("_", " ", $date);

        $fileName = "export_" . $date."_" . rand() . ".csv";
        $filePath = Gpf_Paths::getInstance()->getAccountDirectoryPath().
        self::EXPORT_DIRECTORY . $fileName;

        $this->sendToExportObjects($filePath);
        $this->writeToExportTable($fileName);

        $form->setInfoMessage($this->_("Data was successfully exported to file %s", $fileName));
         
        return $form;
    }

    /**
     * Import data from csv file
     *
     * @service import_export import
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function importCSV(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $importTask = new Gpf_Csv_ImportTask($form);

        try {
            $importTask->run();
            $this->appendOutputMessage($importTask->getOutputMessage());
            $form->setInfoMessage($this->outputMessage);
            $form->setField(self::SUCCESS, Gpf::YES);
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $form->setField(self::SUCCESS, Gpf::NO);
            $this->appendOutputMessage($importTask->getOutputMessage());
            $form->setInfoMessage($this->outputMessage . $this->getMessageSeparator() . $e->getMessage());
        } catch (Exception $e) {
            $importTask->forceFinishTask();
            $form->setField(self::SUCCESS, Gpf::YES);
            $this->appendOutputMessage($importTask->getOutputMessage());
            $form->setErrorMessage($this->outputMessage . $this->getMessageSeparator() . $this->_('Error during Import') . ' (' . $e->getMessage() . ') ');
        }
         
        return $form;
    }

    private function getMessageSeparator() {
        return '*#*info-separator*#*';
    }

    private function appendOutputMessage($outputMessage) {
        if ($outputMessage == '') {
            return;
        }
        if ($this->outputMessage == '') {
            $this->outputMessage = $outputMessage;
            return;
        }
        $this->outputMessage .= '<br />' . $outputMessage;
    }

    private function sendToExportObjects($fileName) {
        $this->codes = preg_split("/,/", $this->codes);
         
        foreach ($this->codes as $code) {
            $className = Gpf_Db_ImportExport::getClassNameFromCode($code);
             
            $params = new Gpf_Rpc_Params();
            $params->add(Gpf_Rpc_Params::CLASS_NAME, $className);
            $params->add(Gpf_Rpc_Params::METHOD_NAME, "exportData");
            $params->add("fileName", $fileName);
            $params->add("delimiter", $this->delimiter);

            $class = new Gpf_Rpc_ServiceMethod($params);
            $class->invoke($params);
        }
    }

    private function writeToExportTable($fileName) {
        $export = new Gpf_Db_Export();
        $export->setFileName($fileName);
        $export->setDateTime($this->date);
        $export->setDescription($this->note);
        $export->setDataTypes($this->getDataTypes());
        $export->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
         
        $export->insert();
    }

    private function getDataTypes() {
        $dataTypes = "";
        foreach ($this->codes as $code) {
            $dataTypes .= $this->_localize(Gpf_Db_ImportExport::getNameFromCode($code)).",";
        }
         
        return substr($dataTypes, 0 , -1);
    }
}

?>
