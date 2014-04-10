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

class Nostress_Nscexport_Model_Data_Reader extends Nostress_Nscexport_Model_Abstract
{   	
	const SOURCE_ENCODING = "src_encoding";
	const DESTINATION_ENCODING = "dst_encoding";
	const SKIP_FIRST = "skip_first_record";	
	const HTTP_PREFIX = 'http';
	const TMP_FILES_DIR = 'var/koongoConnector/';
	const URL_PATH_DELIMITER = "/";
	
	protected $_reader;
	protected $_fileType;
	protected $_filePath = "";
	protected $_tmpFilePath = "";
	protected $_params;
	protected $_dstEncoding = 'utf-8';
	protected $_srcEncoding = null;
	protected $_skipFirst = 0;
	
	public function getRemoteFileContent($url)
	{
		if(empty($url))
			return "";
		$offset = strripos($url, "/") + 1; 
		$filename = substr($url,$offset);
		$params = array(self::FILE_PATH => $url,self::FILE_NAME => $filename);
		$this->initSimpleParams($params);
		$this->openFile(array());
		$result = $this->getFileContentAsString();
		$this->closeFile();
		return $result;
	}
	
	public function getTaxonomyFileContent($params)
	{
		$this->initTaxonomyParams($params);
		$this->openFile($params);
		$result = $this->getAllRecords();
		$this->closeFile();
		return $result;
	}
	
	public function openFile($params)
	{		
		$result = false;
		switch($this->_fileType)
		{
			case self::TYPE_CSV:
				$this->_reader = Mage::getModel('nscexport/data_reader_abstract_csv');
				break;
			case self::TYPE_TEXT:
				$this->_reader = Mage::getModel('nscexport/data_reader_abstract_text');
				break;
			default:
				$this->_reader = Mage::getModel('nscexport/data_reader_abstract');
				break;
		}
		$this->downloadFileToLocalDirectory();
		return $this->_reader->openFile($this->_tmpFilePath,$params);
	}
	
	public function getAllRecords()
	{
		$result = array();
		$record = $this->getRecord();
		
		if($this->_skipFirst)
			$record = $this->getRecord();
			
		while($record != false)
		{
			$result[] = $record;
			$record = $this->getRecord();
		}
		return $result;
	}
	
	public function getFileContentAsString()
	{
		$content = "";
		$record = $this->getRecord();
		while($record != false)
		{
			$content .= $record;
			$record = $this->getRecord();
		}
		return $content;
	}
	
	protected function initSimpleParams($params)
	{
		$this->initParam($this->_filePath,$params[self::FILE_PATH]);
		$this->initParam($this->_tmpFilePath,self::TMP_FILES_DIR.$params[self::FILE_NAME]);
	}
	
	protected function initTaxonomyParams($params)
	{
		$this->initParam($this->_dstEncoding,$params[self::DESTINATION_ENCODING]);
		$this->initParam($this->_srcEncoding,$params[self::SOURCE_ENCODING]);	
		$this->initParam($this->_fileType,$params[self::FILE_TYPE]);
		$path = $this->initFilePath($params[self::FILE_PATH]);		
		$this->initParam($this->_filePath,$path.$params[self::FILE_NAME]);
		$this->initParam($this->_tmpFilePath,self::TMP_FILES_DIR.$params[self::FILE_NAME]);
		$this->initParam($this->_skipFirst,$params[self::SKIP_FIRST]);
	}
	
	protected function initFilePath($path)
	{
		if(str_word_count($path,0,self::URL_PATH_DELIMITER) <= 1 && strpos($path,self::HTTP_PREFIX) == false)
			$path = (string)$this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_TAXONOMY_SOURCE_URL).$path;	
		return $path;
	}
	
	protected function initParam(&$param,$value)
	{
		if(isset($value) && !empty($value))
			$param = $value;
	}
	
	public function getRecord()
	{
		if(isset($this->_reader))
			return $this->helper()->changeEncoding($this->_dstEncoding,$this->_reader->getRecord(),$this->_srcEncoding) ;
		else 
			return false;
	}
	
	public function getRecordNoEncodingChange()
	{
		return $this->_reader->getRecord();
	}
	
	protected function closeFile()
	{
		if(isset($this->_reader))
		{	
		    $result = $this->_reader->closeFile();
		    $this->helper()->deleteFile($this->_tmpFilePath);
		    return $result;
		}
		else 
			return false;
	}
	
    protected function downloadFileToLocalDirectory()
    {
        $this->helper()->createDirectory(self::TMP_FILES_DIR);	 
        $this->helper()->downloadFile($this->_filePath,$this->_tmpFilePath);
    }
	
	protected function helper()
    {
    	return Mage::helper('nscexport');
    }	
}
?>