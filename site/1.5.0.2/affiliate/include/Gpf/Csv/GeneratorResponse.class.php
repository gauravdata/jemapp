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

class Gpf_Csv_GeneratorResponse extends Gpf_Object  {

    private $fileName;
    private $header = null;

    /**
     * @var Gpf_Data_RecordSet
     */
    private $csv;
    private $buffer;
    private $delimiter;

    /**
     * Generate CSV file to output from RecordSet $cvs
     *
     * @param String $fileName
     * @param array $header
     * @param Gpf_Data_RecordSet $csv
     */
    function __construct($fileName, $header = null, $csv = null, $fileHeader = null, $deilimiter = ',') {
        if ($header != null) {
            $this->header = $header;
        }

        $this->fileName = $fileName;
        $this->csv = $csv;
        $this->buffer = Gpf_Csv_ObjectImportExport::UTF8BOM_HEADER;
        $this->delimiter = $deilimiter;
        $this->encodeHeader($fileHeader, $header);
    }

    public function generateFile() {
        $this->buildData();
        return $this->getFile();
    }

    /**
     * @return Gpf_File_Download_String
     */
    public function getFile() {
        $file = new Gpf_File_Download_String($this->fileName, $this->buffer);
        $file->setAttachment(true);
        return $file;
    }
    
    public function getBuffer() {
        return $this->buffer;
    }

    /**
     * @param String $delimiter
     */
    public function setDelimiter($delimiter = ",") {
        $this->delimiter = $delimiter;
    }

    public function add(Gpf_Data_Record $record) {
        if ($this->header == null) {
            $this->encode($record->toObject());
            return;
        }

        $this->addFilteredByHeader($record);
    }
     
    private function addFilteredByHeader(Gpf_Data_Record $record) {
        $newRecord = new Gpf_Data_Record($this->header);
        foreach ($this->header as $column) {
            $newRecord->set($column, $record->get($column));
        }
        $this->encode($newRecord->toObject());
    }

    private function buildData() {
        foreach ($this->csv as $record) {
            $row = $record->toObject();
            $this->encode($row);
        }
    }

    protected function encode($array) {
        if ( is_null($array) || count($array) == 0 ) {
            return;
        }

        for ($i = 0; $i < count($array); $i++) {
            if (strpos($array[$i], "\"")) {
                $array[$i] = str_replace("\"", "\"\"", $array[$i]);
                $array[$i] = "\"$array[$i]\"";
            } elseif (strpos($array[$i], $this->delimiter)) {
                $array[$i] = "\"$array[$i]\"";
            }
        }
        $this->buffer .= implode($this->delimiter, $array)."\n";
    }

    /**
     * @param array||null $fileHeader
     */
    protected function encodeHeader($fileHeader, $header) {
		if (!is_null($fileHeader)) {
        	$this->encode($fileHeader);
        	return;
        }
        $this->encode($header);
    }
}
?>
