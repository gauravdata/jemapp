<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Gpf_Csv_ExportDB.class.php 19023 2008-07-08 12:50:59Z mfric $
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

class Gpf_Csv_ExportFiles extends Gpf_Object {

    private $exportFiles;

    /**
     * @var Gpf_Csv_GeneratorFile
     */
    private $file;
    
    function __construct(Gpf_Csv_GeneratorFile $file = null) {
       $this->file = $file;
       $this->exportFiles = array();    
    }
    
    public function addFile(Gpf_Io_File $file = null) {
        $this->exportFiles[] = $file;
    }
    
    public function getFiles() {
    	return $this->exportFiles;
    }
    
    public function addAllExportFilesToFile() {
        if ($this->file == null) {
            throw new Gpf_Exception($this->_("File to export is not defined"));
        }
        foreach ($this->exportFiles as $exportFile) {
            $this->writeExportFileToFile($exportFile);
            $this->file->writeRawString(Gpf_Csv_ImportExportService::DATA_DELIMITER);
        }
    }
    
    public function setExportFile(Gpf_Csv_GeneratorFile $file) {
        $this->file = $file;
    }
    
    private function writeExportFileToFile(Gpf_Io_File $exportFile) {
        $this->file->writeRawString($exportFile->getFileName()."\n");
        while ($row = $exportFile->readLine()) {
            $this->file->writeArray(array($row));
        }
    }
}

?>
