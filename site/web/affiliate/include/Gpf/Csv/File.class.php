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
 * @package GwtPhpFramework
 */

class Gpf_Csv_File extends Gpf_Object {

    private $enclosure;
    private $delimiter;
    /**
     * @var Gpf_Io_File
     */
    private $file;

    private $actualLineNumber;
    private $actualCSVLineNumber;

    /**
     * Generate CSV file to path from $fileName
     *
     * @param String $fileName
     */
    function __construct($fileName, $fileMode) {
        $this->file = new Gpf_Io_File($fileName);
        $this->file->setFileMode($fileMode);
        $this->file->setFilePermissions(0777);
        $this->delimiter = ",";
        $this->enclosure = '"';
        $this->actualLineNumber = 0;
    }

    /**
     * @param String $delimiter
     */
    public function setDelimiter($delimiter = ",") {
        $this->delimiter = $delimiter;
    }

    public function delete() {
        $this->file->delete();
    }

    public function isExists() {
        return $this->file->isExists();
    }

    /**
     * @param String $enclosure
     */
    public function setEnclosure($enclosure = '"') {
        $this->enclosure = $enclosure;
    }

    public function readArray() {
        $csvLine = null;
        $this->actualCSVLineNumber = null;
        while ($line = $this->file->readLine()) {
            $this->actualLineNumber++;
            if (is_null($this->actualCSVLineNumber)) {
                $this->actualCSVLineNumber = $this->actualLineNumber;
            }
            if ($csvLine === null) {
                $csvLine = $line;
                if ($this->isCsvLine($csvLine)) {
                    break;
                }
                continue;
            }
            $csvLine .= $line;
            if ($this->isCsvLine($csvLine)) {
                break;
            }
        }
        if ($csvLine === null) {
            return false;
        }
        $csvLine = ltrim($csvLine, Gpf_Csv_ObjectImportExport::UTF8BOM_HEADER);
        if (substr($csvLine, -1) == "\n" ) {
            $csvLine = substr($csvLine, 0, -1);
        }
        return Gpf_Csv_Parser::parse($csvLine, $this->delimiter);
    }

    public function getActualLineNumber() {
        return $this->actualLineNumber;
    }

    public function getActualCSVLineNumber() {
        return $this->actualCSVLineNumber;
    }

    public function rewind() {
        $this->actualLineNumber = 0;
        $this->file->rewind();
    }

    public function readRawString() {
        $this->actualLineNumber++;
        return $this->file->readLine();
    }

    /**
     * @param String $string
     */
    public function writeRawString($string) {
        $this->file->write($string);
    }

    public function setFileName($name) {
        $this->file->setFileName($name);
    }

    public function setFileMode($mode) {
        $this->file->setFileMode($mode);
    }

    /**
     * @param array $array
     */
    public function writeArray($array) {
        if ($array != null) {
            $this->encode($array);
        }
    }

    /**
     * @param Gpf_Data_Record $record
     */
    public function writeRecord($record) {
        if ($record != null) {
            $this->encode($record->toObject());
        }
    }

    /**
     * @param Gpf_Data_RecordSet $recordSet
     */
    public function writeData(Gpf_Data_RecordSet $recordSet) {
        foreach ($recordSet as $record) {
            $row = $record->toObject();
            $this->encode($row);
        }
    }

    public function getFileName() {
        return $this->file->getFileName();
    }

    public function close() {
        $this->file->close();
    }

    private function encode($array) {
        $this->file->writeCsv($array, $this->delimiter);
    }

    private function isCsvLine($string) {
        if ((substr_count($string, $this->enclosure) % 2) == 0) {
            return true;
        }
        return false;
    }
}
