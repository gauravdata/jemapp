<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExistingExportsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Gpf_Csv_ServerExportFilesGrid extends Gpf_View_GridService {
    
    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Exports::FILENAME, $this->_("File name"), true);
        $this->addViewColumn(Gpf_Db_Table_Exports::DATETIME, $this->_("Date"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_Exports::DESCRIPTION, $this->_("Description"), true);
        $this->addViewColumn(Gpf_Db_Table_Exports::DATA_TYPES, $this->_("Modules"), true);
        $this->addViewColumn("size", $this->_("Size"), true);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Exports::ID);
        $this->addDataColumn(Gpf_Db_Table_Exports::FILENAME);
        $this->addDataColumn(Gpf_Db_Table_Exports::DATETIME);
        $this->addDataColumn(Gpf_Db_Table_Exports::DESCRIPTION);
        $this->addDataColumn(Gpf_Db_Table_Exports::DATA_TYPES);
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Exports::FILENAME, '', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Exports::DATETIME, '', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Exports::DESCRIPTION, '', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Exports::DATA_TYPES, '', 'N');
        $this->addDefaultViewColumn("size", '', 'N');
    }
    
    protected function buildFrom(){
    	$this->_selectBuilder->from->add(Gpf_Db_Table_Exports::getName());
    }
    
    /**
     *
     * @param Gpf_Data_RecordSet $inputResult
     * @return Gpf_Data_RecordSet
     */
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
    	$inputResult->addColumn("size", null);
    	
    	foreach ($inputResult as $record) {
    		$fileData = $this->getFileData($record->get(Gpf_Db_Table_Exports::FILENAME));
    		if (count($fileData) > 1) {
    			$record->set("size", $fileData[1]);
    		} else {
    			$record->set(Gpf_Db_Table_Exports::FILENAME, $fileData[0]);
    			$record->set("size", 0);
    		}
    	}
    	
        return $inputResult;
    }
    
    /**
     * @service export_file read
     *
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
    	return parent::getRows($params);
    }
    
    /**
     * @param String $fileName
     * @return array
     */
    private function getFileData($fileName) {
    	$path = Gpf_Paths::getInstance()->getAccountDirectoryPath().Gpf_Csv_ImportExportService::EXPORT_DIRECTORY;
    	$file = new Gpf_Io_File($path . $fileName);
    	
    	$array = array();
    	if ($file->isExists()) {
    		$array[] = $fileName;
    		$array[] = $file->getSize();
    		return $array;
    	}
    	
    	$array[] = $this->_('file not exist');
    	return $array;
    }
    
    
    
    private function readFiles(Gpf_Data_RecordSet $result) {
    	$path = Gpf_Paths::getInstance()->getAccountDirectoryPath().Gpf_Csv_ImportExportService::EXPORT_DIRECTORY;
    	
        foreach (new Gpf_Io_DirectoryIterator($path, 'csv') as $fullFileName => $fileName) {
            $file = new Gpf_Io_File($fullFileName);
            $file->setFileMode("r");
            
            if ($fileHeader = $file->readCsv(";")) {
                if ($fileHeader[0] == null) {
                    return;
                }
                $url = Gpf_Paths::getInstance()->getFullBaseServerUrl() . 
                    Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() . 
                        Gpf_Csv_ImportExportService::EXPORT_DIRECTORY . $fileName;
                $result->add(array($url, $fileName, $fileHeader[2], $fileHeader[3], 
                    $this->getDataTypes($fileHeader[1]), $file->getSize()));
                    }
        }
    }
    
    private function getDataTypes($codes) {
    	$codes = preg_split("/,/", $codes);
    	
    	$select = new Gpf_SqlBuilder_SelectBuilder();
    	$select->select->add(Gpf_Db_Table_ImportExports::NAME);
    	$select->from->add(Gpf_Db_Table_ImportExports::getName());
    	foreach ($codes as $code) {
    		$select->where->add(Gpf_Db_Table_ImportExports::CODE, "=", $code, "OR");
    	}
    	$records = $select->getAllRows();
    	
    	$modules = "";
    	foreach ($records as $record) {
    		$modules .= $record->get(Gpf_Db_Table_ImportExports::NAME).",";
    	}
    
    	return substr($modules, 0, -1);
    }
}
?>
