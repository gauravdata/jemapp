<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ObjectImportExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
abstract class Gpf_Csv_ObjectImportExport extends Gpf_Tasks_LongTask {

	const OBJECT_DELIMITER = "\n\n";
	const DATA_DELIMITER = "\n";
	const UTF8BOM_HEADER = "\xEF\xBB\xBF";

	protected $name;
	protected $partName;
	private $description;
	/**
	 * @var boolean
	 */
	protected $delete;
	protected $code;
	protected $delimiter;
	protected $dataHeader = null;
	protected $fileUrl;

    protected $outputMessage;
    protected $importedLinesCount;
    protected $wrongLinesCount;

	/**
	 * @var array
	 */
	protected $requiredColumns = null;
	/**
	 * @var Gpf_Csv_File
	 */
	protected $file;
	/**
	 * @var array
	 */
	protected $paramsArray = array();
	/**
	 * @var Gpf_Log_Logger
	 */
	protected $logger;

	public function __construct() {
		$this->logger = Gpf_Log_Logger::getInstance();
		$this->logger->add(Gpf_Log_LoggerDatabase::TYPE, Gpf_Log::DEBUG);
        $this->outputMessage = '';
        $this->importedLinesCount = 0;
        $this->wrongLinesCount = 0;
	}
	
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

	/**
	 * Export data to file
	 *
	 * @service import_export export
	 * @param Gpf_Rpc_Params $params
	 */
	public function exportData(Gpf_Rpc_Params $params) {
		$this->file = new Gpf_Csv_File($params->get("fileName"), 'a');
		$this->delimiter = $params->get("delimiter");
		$this->file->setDelimiter($this->delimiter);
			
		$this->writeObjectHeader();
		$this->writeData();
		$this->writeObjectFooter();
	}

	/**
	 * Import data from file
	 *
	 * @service import_export import
	 * @param Gpf_Rpc_Params $params
	 */
	public function importData(Gpf_Rpc_Params $params) {
		$this->file = new Gpf_Csv_File($params->get("fileUrl"), 'r');
		$this->delimiter = $params->get("delimiter");
		$this->fileUrl = $params->get("fileUrl");
		$this->file->setDelimiter($this->delimiter);
		$this->delete = ($params->get("delete") == Gpf::YES ? true : false);
		$this->run($params->get('startTime') + 24 - time());
	}

	public function setName($name) {
		$this->name = $name;
        if (preg_match_all('/^##(.+?)##$/ms',  $this->name, $attributes)) {
            $this->code = strtoupper('[['.$attributes[1][0].']]');
            return;
        }
		$this->code = strtoupper('[['.$this->name.']]');
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getName() {
		return $this->name;
	}

    public function getPartName() {
        return $this->partName;
    }

	public function getCode() {
		return $this->code;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setRequiredColumns(array $requiredColumns) {
		$this->requiredColumns = $requiredColumns;
	}

	public function getRequiredColumns() {
		return $this->requiredColumns;
	}

	protected abstract function checkData();

	protected abstract function writeData();

	protected abstract function deleteData();

	protected abstract function readData();

	/**
	 * Write data from $selectBuilder to export file
	 *
	 * @param Gpf_SqlBuilder_SelectBuilder $selectBuilder
	 */
	public function writeSelectBuilder(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
		$headerColumns = $this->getArrayHeaderColumns($selectBuilder);
		$this->file->writeArray($this->getHeaderArray($headerColumns));
		$mysqlStatement = $this->createDatabase()->execute($selectBuilder->toString());
		while ($row = $mysqlStatement->fetchRow()) {
			$this->file->writeArray($row);
		}
	}

	/**
	 * Read data to $dbRow object, $columns format (key = $this->encodeColumn(columnAlias), value = columnName)
	 *
	 * @param String $dbRowName
	 * @param array $columns
	 */
	public function readDbRow($dbRowName, array $columns) {
		if ($this->findCode()) {
            if (isset($this->paramsArray['importedLinesCount'])) {
                $this->importedLinesCount = $this->paramsArray['importedLinesCount'];
            }
            if (isset($this->paramsArray['wrongLinesCount'])) {
                $this->wrongLinesCount = $this->paramsArray['wrongLinesCount'];
            }
			$this->findDataHeader();
			$headerArray = $this->readHeaderArray();
			$headerColumns = $this->mapHeaderColumns($columns, $headerArray);

            if ($this->partName != null && $this->partName != '') {
                $this->appendOutputMessage($this->getPartNameMessage($this->partName));
            }
			$this->importRows($dbRowName, $headerColumns, $headerArray);
		}
	}

	protected function importRows($dbRowName, array $columns, array $headerArray) {
		while ($row = $this->file->readArray()) {
            $this->logger->debug('Reading row ('.$this->file->getActualCSVLineNumber().'): ' . implode($this->delimiter, $row));
            $this->paramsArray['importedLinesCount'] = $this->importedLinesCount;
            $this->paramsArray['wrongLinesCount'] = $this->wrongLinesCount;
			if ($this->isPending(implode($this->delimiter, $row), $this->_('Importing %s rows', $this->getName()))) {
				if ($this->hasData($row)) {
					$class = new ReflectionClass($dbRowName);
					if (count($row) >= count($headerArray)) {
						$dbRow = $class->newInstance();

						for ($i = 0; $i < count($headerArray); $i++) {
							if (array_key_exists($headerArray[$i], $columns)) {
								$dbRow->set($columns[$headerArray[$i]], $row[$i]);
							}
						}
						$this->beforeSave($dbRow);
						try {
							try {
								$this->insert($dbRow);
                                $this->incrementSuccessfulCount();
                                $this->logger->debug('Data from line: ' . $this->file->getActualCSVLineNumber() . ' was inserted.');
							} catch (Gpf_DbEngine_DuplicateEntryException $e) {
								$dbRow->update();
                                $this->incrementSuccessfulCount();
                                $this->logger->debug('Data from line: ' . $this->file->getActualCSVLineNumber() . ' was updated.');
							}
						} catch (Gpf_Exception $e) {
							$this->logError($this->getName(), $row, $e);
                            $this->appendOutputMessage($this->getSaveErrorMessage($this->file->getActualCSVLineNumber(), $e->getMessage()));
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

    protected function getPartNameMessage($partName) {
        return $this->getStyleText($this->_('Import %s', $partName), 'font-style: italic');
    }

    protected function getSaveErrorMessage($lineNumber, $exceptionMessage) {
        return $this->getRowNumberMessage($lineNumber, true) . $this->_('Could not import data. Exception: %s', $exceptionMessage);
    }

    protected function getLessItemsMessage($lineNumber, $addStyle = true) {
        return $this->getRowNumberMessage($lineNumber, $addStyle) . $this->_('Number of itmes in row do not match with number of header items.');
    }

    protected function getRowNumberMessage($lineNumber, $addStyle = true) {
        $style = null;
        if($addStyle) {
            $style = 'color: red'; 
        }
	    return $this->getStyleText($this->_('Row %s: ', $lineNumber), $style);
	}

    protected function getEndDataMessage($lineNumber, $name) {
        return $this->_('Row %s has not any data. End of importing %s.', $lineNumber, $name);
    }

    protected function getEndFileMessage($name) {
        return $this->_('End of file. End of importing %s.', $name);
    }

    protected function appendOutputData($importedRows, $wrongRows) {
        $this->appendOutputMessage($this->getStyleText($this->_('Imported rows: %s', $importedRows), 'font-weight: bold; color: green'));
        if ($wrongRows > 0) {
            $this->appendOutputMessage($this->getStyleText($this->_('Wrong rows: %s', $wrongRows), 'font-weight: bold; color: red'));
        }
    }

    protected function appendOutputMessage($outputMessage) {
        if ($outputMessage == '') {
            return;
        }
        if ($this->outputMessage == '') {
            $this->outputMessage = $outputMessage;
            return;
        }
        $this->outputMessage .= '<br /> '.$outputMessage;
    }

    protected function getStyleText($text, $style) {
        if ($style == null) {
            return $text;
        }
        return '<span style=\''. $style .'\'>' . $text . '</span>';
    }

	protected function incrementSuccessfulCount() {
	    $this->importedLinesCount++;
	}

    protected function incrementWrongCount() {
        $this->wrongLinesCount++;
    }

    protected function resetImportedCounts() {
        $this->importedLinesCount = 0;
        $this->wrongLinesCount = 0;
        $this->paramsArray['importedLinesCount'] = 0;
        $this->paramsArray['wrongLinesCount'] = 0;
    }

    protected function insert(Gpf_DbEngine_Row $dbRow) {
        $dbRow->insert();
    }

	protected function mapHeaderColumns(array $columns, array $headerArray) {
		$columnsArray = array();

		foreach ($headerArray as $headerColumn) {
			if (array_key_exists($headerColumn, $columns)) {
				$columnsArray[$headerColumn] = $columns[$headerColumn];
			}
		}

		return $columnsArray;
	}

	protected function removeAlias(array $columns) {
		$array = array();
		foreach ($columns as $key => $value) {
			if (preg_match('/[^\.]*$/', $value, $array)) {
				$columns[$key] = $array[0];
			}
		}

		return $columns;
	}

	protected function checkFile(array $header) {
		if ($this->findCode()) {
			if ($this->dataHeader !== null) {
				$this->checkDataHeader();
			}
			$headerArray = $this->checkRowsHeader($header);
			$this->checkRows($header, $headerArray);
		}
	}

	protected function checkRows(array $headerColumns, array $headerArray) {
		$count = count($headerArray);
		while ($row = $this->file->readArray()) {
			if ($this->isPending(implode($this->delimiter, $row), $this->_('Check %s rows', $this->getName()))) {
				if ($this->hasData($row)) {
					$this->checkRequiredColumns($headerColumns, $headerArray, $row);
				} else {
					$this->setDone();
					break;
				}
				$this->setDone();
			}
		}
	}

	protected function checkRequiredColumns(array $headerColumns, array $headerArray, array $row) {
		if ($this->requiredColumns != null) {
			for ($i = 0; $i < count($row); $i++) {
				if (in_array($headerArray[$i], $this->requiredColumns)) {
					if ($row[$i] == "") {
						$errorMsg = 'Required column ' . $headerArray[$i] .
                		  ' in ' . $this->code . ' ' . $this->dataHeader . ' is empty';
						$this->logger->error($errorMsg);
						throw new Gpf_Csv_NoCorrectImportFileException($errorMsg);
					}
				}
			}
		}
	}

	protected function checkRowsHeader(array $header) {
		if ($headerArray = $this->file->readArray()) {
			if ($this->hasData($headerArray)) {
				$headerColumns = $this->mapHeaderColumns($header, $headerArray);
				$array = array_diff_assoc($header, $headerColumns);
				if (count($array) > 0) {
					$errorMsg = 'Rows header ' . implode($this->delimiter, $headerArray) .
                	   ' in ' . $this->code . ' is not correct. Missing column(s) ' . implode($this->delimiter, array_keys($array));
					$this->logger->error($errorMsg);
					throw new Gpf_Csv_NoCorrectImportFileException($errorMsg);
				}
				return $headerArray;
			}
		}
		return array();
	}

	protected function checkDataHeader() {
		while ($line = $this->file->readArray()) {
			if ($line[0] == $this->dataHeader) {
				return;
			}
		}
		$errorMsq = 'Data header ' . $this->dataHeader .
            ' in ' . $this->getCode() . ' is not correct or is missing';
		$this->logger->error($errorMsq);
		throw new Gpf_Csv_NoCorrectImportFileException($errorMsq);
	}

	/**
	 * @param Gpf_SqlBuilder_SelectBuilder $selectBuilder
	 * @return array
	 */
	public function getArrayHeaderColumns(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
		$selectColumns = $selectBuilder->select->getColumns();
		$headerColumns = array();

		foreach ($selectColumns as $selectColumn) {
			$headerColumn = $this->getHeaderColumn($selectColumn);
			$headerColumns[$headerColumn[0]] = $headerColumn[1];
		}

		return $headerColumns;
	}

    public function getOutputMessage() {
        return $this->outputMessage;
    }

    public function getImportedLinesCount() {
        return $this->importedLinesCount;
    }

    public function getWrongLinesCount() {
        return $this->wrongLinesCount;
    }

	/**
	 * @param String $dataHeader
	 */
	public function writeDataHeader($dataHeader) {
		$this->setDataHeader($dataHeader);
		$this->file->writeArray(array($this->dataHeader));
	}

	public function setDataHeader($dataHeader = null) {
		$this->dataHeader = "[$dataHeader]";
	}

	private function getHeaderColumn(Gpf_SqlBuilder_SelectColumn $selectColumn) {
		return array($this->encodeColumn($selectColumn->getAlias()), $selectColumn->getName());
	}

	private function getHeaderArray(array $headerColumns) {
		$headerArray = array();
		foreach ($headerColumns as $key => $value) {
			$headerArray[] = $key;
		}
			
		return $headerArray;
	}
	
	protected function writeUTF8BomHeader() {
	    $this->file->writeRawString(self::UTF8BOM_HEADER);
	}

	protected function writeObjectHeader() {
        $this->writeUTF8BomHeader();
		$this->file->writeArray(array($this->code));
	}

	protected function writeDataDelimiter() {
		$this->file->writeRawString(self::DATA_DELIMITER);
	}

	protected function writeObjectFooter() {
		$this->file->writeRawString(self::OBJECT_DELIMITER);
	}

	protected function rewindFile() {
		$this->file = new Gpf_Csv_File($this->fileUrl, 'r');
		$this->file->setDelimiter($this->delimiter);
	}

	protected function encodeColumn($column) {
		return strtoupper('!'.$column);
	}

	protected function hasData(array $row) {
		foreach ($row as $column) {
			if ($column !== '') {
				return true;
			}
		}
		return false;
	}

	protected function execute() {
		if ($this->isBlockPending('check')) {
			$this->logger->debug('Check ' . $this->getName());
			$this->checkData();
			$this->setBlockDone();
		}

		if ($this->delete &&
		$this->isBlockPending('delete')) {
			$this->logger->debug('Delete ' . $this->getName());
			$this->deleteData();
			$this->setBlockDone();
		}
		if ($this->isBlockPending('import')) {
			$this->logger->debug('Import ' . $this->getName());
			$this->readData();
			$this->setBlockDone();
		}
	}

	/**
	 * @return boolean
	 */
	protected function findCode() {
		while ($line = $this->file->readArray()) {
			if ($line[0] == $this->code) {
				return true;
			}
			$this->checkInterruption();
		}
		return false;
	}

	protected function findDataHeader() {
		if ($this->dataHeader !== null) {
			while ($line = $this->file->readArray()) {
				if ($line[0] == $this->dataHeader) {
					return;
				}
				$this->checkInterruption();
			}
		}
	}

	/**
	 * @return array
	 */
	protected function readHeaderArray() {
		if ($headerArray = $this->file->readArray()) {
			if ($this->hasData($headerArray)) {
				return $headerArray;
			}
		}
		throw new Gpf_Csv_NoCorrectImportFileException($this->_('Incorect data header'));
	}

	protected function loadTask() {
		$this->task->setClassName(get_class($this));
		$this->task->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
		$this->task->loadFromData();
	}

	protected function loadFromTask() {
		parent::loadFromTask();
		$json = new Gpf_Rpc_Json();
		$values = $json->decode($this->getParams());
		if (isset($values->isPending)) {
			$this->paramsArray['isPending'] = $values->isPending;
            if (isset($values->importedLinesCount)) {
                $this->paramsArray['importedLinesCount'] = $values->importedLinesCount;
            } else {
                $this->paramsArray['importedLinesCount'] = 0;
            }
            if (isset($values->importedLinesCount)) {
                 $this->paramsArray['wrongLinesCount'] = $values->wrongLinesCount;
            } else {
                $this->paramsArray['wrongLinesCount'] = 0;
            }
		}
	}

	protected function updateTask() {
		if ($this->doneProgress !== null) {
			$json = new Gpf_Rpc_Json();
			$this->task->setParams($json->encode($this->paramsArray));
			$this->task->setProgress($this->doneProgress);
			$this->task->setProgressMessage($this->getProgressMessage());
			$this->task->updateTask();
		}
	}

	protected function isBlockPending($code) {
		if (!isset($this->paramsArray['isPending']) || $this->paramsArray['isPending'] === $code) {
			$this->paramsArray['isPending'] = $code;
			return true;
		}
		return false;
	}

	protected function setBlockDone() {
		unset($this->paramsArray['isPending']);
	}

	protected function beforeSave(Gpf_DbEngine_Row $dbRow) {
	}
	
	protected function logError($importObject, array $row, Gpf_Exception $e) {
		$this->logger->error($this->_sys('Could not import %s from %s on line %s. Throw exception: %s', $importObject, $this->file->getFileName(), implode($this->delimiter, $row), $e->getMessage()));
	}
}
?>
