<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ImportDB.class.php 19023 2008-07-08 12:50:59Z mfric $
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

class Gpf_Csv_ImportFiles extends Gpf_Object {
    
    private $importFiles;
    /**
     * @var Gpf_Io_File
     */
    private $file;
    private $delimiter;
    
    function __construct(Gpf_Io_File $file = null) {
       $this->file = $file;
       $this->importFiles = array();
    }
    
    public function setImportFile(Gpf_Io_File $file) {
        $this->file = $file;    
    }
    
    public function setImportFiles($importFiles) {
    	$this->importFiles = $importFiles;
    }
    
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }
    
    public function addImportFile(Gpf_Io_File $importFile) {
        $this->importFiles[] = $importFile;
    }
    
    public function addFilesToImportFiles() {
        if ($this->file == null) {
            throw new Gpf_Exception($this->_("Import file is not defined"));
        }
        foreach ($this->importFiles as $importFile) {
            $this->importDataToImportFile($importFile);
        }
    }
    
    private function importDataToImportFile(Gpf_Io_File $importFile) {
        $fileName = $importFile->getFileName()."\n";
        $this->file->rewind();
        while ($line = $this->file->readLine()) {
           if ($line == $fileName) {
           	   $importFile->setFileMode("w");
               $this->importRowsToImportFile($importFile);
               break;
           }
        }
    }
    
    private function importRowsToImportFile(Gpf_Io_File $importFile) {
        while ($row = $this->file->readCsv($this->delimiter)) {
            if ($row[0] != null) {
                $importFile->write($row[0]);
            } else {
                break;
            }
        }
    }
}

?>
