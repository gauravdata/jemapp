<?php 
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Taxonomy_Preprocessor extends Mage_Core_Model_Abstract
{   
	const INPUT_COLUMNS = 'input_columns';
	const CAT_PATH_DELIMITER = 'category_path_delimiter';
	
	const CAT_NAME = 'name';
	const CAT_ID = 'id';
	const CAT_PATH = 'path';
	const CAT_IDS_PATH = 'ids_path';
	const CAT_LEVEL = 'level';
	const CAT_PARENT_NAME = 'parent_name';
	const CAT_PARENT_ID = 'parent_id';		
	
	const UNSELECTABLE_CATEGORY_ID = "-1"; 
	const DEF_OUT_DELIMITER = ' > ';
	
	protected $_inputCols = array();
	protected $_outputCols = array(self::CAT_NAME,self::CAT_ID,self::CAT_PATH,self::CAT_IDS_PATH,self::CAT_LEVEL,self::CAT_PARENT_NAME,self::CAT_PARENT_ID);
	protected $_catPathDelim = '>';
	
	protected $_record = array();
	protected $_records = array();
	protected $_catIndexTable = array();
	protected $_categoryIdCounter = 0;
	protected $_addCategoryId = true;
	
	public function init($params)
	{
		$this->initParam($this->_inputCols,$params[self::INPUT_COLUMNS]);
		$this->_inputCols = explode(",",$this->_inputCols);
		if(in_array(self::CAT_ID,$this->_inputCols))
				$this->_addCategoryId = false;
				
		$this->initParam($this->_catPathDelim,$params[self::CAT_PATH_DELIMITER]);		
	}
	
	public function processRecords($records,$params = null)
	{
		if(isset($params))
			$this->init($params);
		$this->_processRecords($records);
		return $this->_postProcess();	
	}
	
	public function _processRecords($records)
	{
		foreach($records as $row)
			$this->processRecord($row);
	}
	
	protected function _postProcess()
	{
		$missingColumns = null;
		foreach($this->_catIndexTable as &$row)
		{
			$missingColumns = array_diff($this->_outputCols,array_keys($row));							
			foreach($missingColumns as $col)
			{				
				$row[$col] = $this->processMissingColumn($col,$row);
			}		
				
		}
		return $this->_catIndexTable;
	}
	
	
	protected function processRecord($row)
	{
		if(!isset($row))
			return;		
		if(!is_array($row))
			$row = array($row);
			
		$this->initRecord();
		
		$oldColumnIndex = "";
		$recordAdded = false;
		foreach($this->_inputCols as $index => $value)
		{
			if($value == self::CAT_NAME && empty($row[$index]))
			{
				$row[$index] = $this->getCatIdIndexedRecordColumnValue($this->getRecordItem(self::CAT_ID), self::CAT_NAME);
			}
			
			if(empty($row[$index]))
				continue;
			
			$recordAdded = false;
			if($oldColumnIndex == $value)
			{
				$this->addRecord();
				$recordAdded = true;
			}
			
			$this->processColumn($value,$row[$index]);			
			
			if($oldColumnIndex == "")
				$oldColumnIndex = $value;
		}	
		
		if(!$recordAdded)
			$this->addRecord();
		
	}
	
	protected function addRecord()
	{
		$this->parseCategoryPath();
		$name = $this->getRecordItem(self::CAT_NAME);
		if($name === false)
		{
			throw new Exception("Missing taxonomy category name.");
		}
		$this->assignCategoryId();
		if(!isset($this->_records[$name]))
			$this->_records[$name] =  $this->_record;
		$id = $this->getRecordItem(self::CAT_ID);
		if($id && !isset($this->_records[$id]))
			$this->_catIndexTable[$id] =  $this->_record;
	}
	
	protected function processColumn($index,$value)
	{	
		$value = trim($value);		
		switch($index)
		{
			case self::CAT_NAME:
				$this->addCategoryToPath($value);
				break;
			default:
				break;
		}
		$this->setRecordItem($index,$value);	
	}
	
	protected function parseCategoryPath()
	{
		$this->_parseCategoryPath($this->getRecordItem(self::CAT_PATH));
	}
	
	protected function addCategoryToPath($name)
	{
		$path = $this->getRecordItem(self::CAT_PATH);
		if(!isset($path) || empty($path))
		{	
			$path = $this->getRecordColumnValue($name,self::CAT_PATH) ;
			if(empty($path))
				$path = $name;
		}
		else
		{
			$path .= self::DEF_OUT_DELIMITER.$name;
		}
		$this->setRecordItem(self::CAT_PATH,$path);
	}
	
	protected function _parseCategoryPath($categoryPath,$setLevel = true,$setName = true,$setParentName = true)
	{
		$result = array();		
		$categories = $this->explodeCategoryPath($categoryPath);
		if($setLevel)
			$this->setRecordItem(self::CAT_LEVEL,count($categories));
		
		$name = array_pop($categories);
		if($setName)
			$this->setRecordItem(self::CAT_NAME,$name);
		
		if($setParentName)
		{
			$parentName = "";
			if(!empty($categories))
				$parentName = array_pop($categories);
			$this->setRecordItem(self::CAT_PARENT_NAME,$parentName);	
		}
	}
	
	
	protected function explodeCategoryPath($categoryPath)
	{
		$path = explode($this->_catPathDelim,$categoryPath);
		if(!isset($path) || !is_array($path))
			$path = array();
		
		foreach($path as &$part)
			$this->trimValue($part); 
		return $path;		
	}
	
	protected function trimValue(&$value) 
	{ 
    	$value = trim($value); 
	}	
	
	protected function setRecordItem($index,$value)
	{
		//if(!isset($this->_record[$index]))
		$this->_record[$index] = $value;
	}
	
	protected function getRecordItem($index)
	{
		if(isset($this->_record[$index]))
			return $this->_record[$index];
		else 
			return false;
	}
	
	protected function initRecord()
	{
		//unset($this->_record);
		$this->_record = array();
	}
	
	protected function assignCategoryId()
	{
		if($this->_addCategoryId)
		{
			$this->setRecordItem(self::CAT_ID,$this->_categoryIdCounter);
			$this->_categoryIdCounter++;
		}
	}
	
	protected function processMissingColumn($index,$row)
	{		
		$value = null;	
		switch($index)
		{
			case self::CAT_PARENT_ID:
				$value = $this->getRecordColumnValue($row[self::CAT_PARENT_NAME],self::CAT_ID);				
				break;
			case self::CAT_PARENT_NAME:
				$value = $this->getCatIdIndexedRecordColumnValue($row[self::CAT_PARENT_ID],self::CAT_NAME);
				break;
			case self::CAT_PATH:
			case self::CAT_IDS_PATH:
			case self::CAT_LEVEL:
				//TODO
				$value = "";
				break;
			case self::CAT_ID:
				$value = self::UNSELECTABLE_CATEGORY_ID;
			default:
				break;
		}
		return $value;
	}
	
	protected function getRecordColumnValue($recordIndex,$columnIndex)
	{
		if(isset($this->_records[$recordIndex][$columnIndex]))	
			return $this->_records[$recordIndex][$columnIndex];
		else 
			return false;
	}
	
	protected function getCatIdIndexedRecordColumnValue($categoryIndex,$columnIndex)
	{
		if(isset($this->_catIndexTable[$categoryIndex][$columnIndex]))
			return $this->_catIndexTable[$categoryIndex][$columnIndex];
		else
			return false;
	}
	
	protected function initParam(&$param,$value)
	{
		if(isset($value) && !empty($value))
			$param = $value;
	}
}
?>