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
* Helper.
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Helper_Data_Check extends Nostress_Nscexport_Helper_Data
{	
	const RES = "result";
	const VAL = "value";
	const CHECK_TYPE_MAGENTO = "magento";
	const CHECK_TYPE_SERVER = "server";
	const CHECK_TYPE_RECOMMEND = "recommend";
	const DEF_MEMORY = 256;
	const DEF_MIN_TIME = 600;
	const DEF_MEDIUM_TIME = 1800;
	const DEF_CORE_URL_ROWS = 100000;
	
	protected $_checkResult;
	
	protected $extensionArray = array(	'curl',
			'dom',
			'gd',
			'hash',
			'iconv',
			'mbstring',
			'mcrypt',
			'pcre',
			'pdo',
			'pdo_mysql',
			'simplexml'	
	);
	
	
	public function run()
	{
		$this->addResultRecord(self::CHECK_TYPE_SERVER,"php",$this->phpVersion());
		$this->addResultRecords(self::CHECK_TYPE_SERVER,$this->safeMode());
		$this->addResultRecords(self::CHECK_TYPE_SERVER,$this->extensions());
		$this->addResultRecord(self::CHECK_TYPE_SERVER,"xslt",$this->xslt());
		$this->addResultRecord(self::CHECK_TYPE_SERVER,"memory",$this->memory());
		$this->addResultRecord(self::CHECK_TYPE_SERVER,"time",$this->time());
		$this->addResultRecord(self::CHECK_TYPE_MAGENTO,"flat_product",$this->flat());
		$this->addResultRecord(self::CHECK_TYPE_MAGENTO,"flat_category",$this->flat('category'));
		$this->addResultRecord(self::CHECK_TYPE_MAGENTO,"indexes",$this->indexes());		
		$this->addResultRecord(self::CHECK_TYPE_MAGENTO,"moduleEnabled",$this->moduleEnabled());
		$this->addResultRecord(self::CHECK_TYPE_RECOMMEND,"compiler",$this->compiler());
		$this->addResultRecord(self::CHECK_TYPE_RECOMMEND,"coreUrlRewrites",$this->coreUrlRewrites());
		
		return $this->getCheckResult();
	}
	
	public function getExtensions()
	{
		return $this->extensionArray;
	}
	
	public function testflat($type = "product")
	{
		$result = $this->flat($type);
		if(isset($result[self::RES]))
			return $result[self::RES];
		else
			return false;
	}
	
	protected function addResultRecord($type = "server",$index,$data)
	{
		$this->_checkResult[$type][$index] = $data;
	
	}
	
	protected function addResultRecords($type = "server",$records)
	{
		$this->_checkResult[$type] = array_merge($this->_checkResult[$type],$records);
	}
	
	protected function resetResult()
	{
		$this->_checkResult = array("server" => array(),"magento" => array());
	}
	
	protected function getCheckResult()
	{
		return $this->_checkResult;
	}
	
	protected function phpVersion()
	{
		$result = array();
		if(version_compare(phpversion(), '5.2.0', '<')) 
			$result[self::RES] = 0;		
		else 
			$result[self::RES] = 1;			
		return $result;
	}
	
	protected function safeMode()
	{
		$result = array();
		if(!ini_get('safe_mode')) 
		{
			$result['safe_mode'] = array(self::RES => 1);
			
			preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $version);
		
			if(version_compare($version[0], '4.1.20', '<')) 
				$result['mysql'] = array(self::RES => 0);						
			else 
				$result['mysql'] = array(self::RES => 1);
		}
		else 
			$result['safe_mode'] = array(self::RES => 0);  
		return $result;
	}
	
	protected function extensions()
	{
		$exts = $this->getExtensions();
		$result = array();
		foreach($exts as $ext)
		{
			if(!extension_loaded($ext))
				$result[$ext] = array(self::RES => 0,self::VAL => $ext);	
			else 
				$result[$ext] = array(self::RES => 1,self::VAL => $ext);
		}
		return $result;
	}
	
	protected function xslt()
	{
		$result = array();
		if(!class_exists("XsltProcessor"))
			$result[self::RES] = 0;	
		else
			$result[self::RES] = 1;			
		
		return $result;
	}
	
	protected function memory()
	{
		$result = array();
		$memory = ini_get('memory_limit');
		$memory = trim($memory,"M");
		$result[self::VAL] = $memory;
		
		if($memory < self::DEF_MEMORY)
			$result[self::RES] = 0;	
		else
			$result[self::RES] = 1;
		
		return $result;
	}
	
	protected function time()
	{
		$result = array();
		
		$time = ini_get('max_execution_time');
		$result[self::VAL] = $time;
		
		if($time < self::DEF_MIN_TIME)
			$result[self::RES] = 0;
		else if($time < self::DEF_MEDIUM_TIME)
			$result[self::RES] = 2;
		else
			$result[self::RES] = 1;
		
		return $result;
	}
	
	protected function flat($type = "product")
	{
		$configPath = Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT;
		if($type == "category")
			$configPath = Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY;
		$result = array();
		$store = Mage::app()->getStore();
		$flatEnabled = Mage::getStoreConfigFlag($configPath, $store);
	
		if($flatEnabled)
			$result[self::RES] = 1;
		else
			$result[self::RES] = 0;
		return $result;
	}
	
	protected function indexes()
	{
		$result = array();
		$collection = Mage::getResourceModel('index/process_collection')->load();
		$indexesRequireReindex = array();
		foreach ($collection as $indexer)
		{
			$status = $indexer->getStatus();
			if($status == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)
			{	
				$processName = ucfirst(str_replace("_", " ", $indexer->getIndexerCode()));
				$indexesRequireReindex[] = $processName;
			}
		}
		
		if(!empty($indexesRequireReindex))
		{
			$result[self::VAL] = implode(" , ", $indexesRequireReindex);
			$result[self::RES] = 0;
		}
		else
			$result[self::RES] = 1;
		return $result;
	}
	
	protected function compiler()
	{
		$result = array();
		if(defined('COMPILER_INCLUDE_PATH'))
			$result[self::RES] = 0;
		else
			$result[self::RES] = 1;
		return $result;		
	}
	
	protected function moduleEnabled()
	{
		$result = array();
		$moduleName = Mage::helper("nscexport/version")->getModuleName();
		$enabled = Mage::helper('core')->isModuleOutputEnabled($moduleName);
		if(!$enabled)
			$result[self::RES] = 0;
		else
			$result[self::RES] = 1;
		return $result;	
	}
	
	protected function coreUrlRewrites()
	{
		$result = array();
		$collection = Mage::getModel('core/url_rewrite')->getCollection();
		$select = $collection->getSelect();
		$select->reset(Zend_Db_Select::COLUMNS);
    	$select->columns("COUNT(*) as rows_count");
    	$collection->load();
    	$columnsCount = 0;
    	foreach ($collection as $record)
    		$columnsCount = $record->getRowsCount();
    	
    	$result[self::VAL] = $columnsCount;
		if($columnsCount > self::DEF_CORE_URL_ROWS)
			$result[self::RES] = 0;
		else
			$result[self::RES] = 1;
		return $result;	
	}
}