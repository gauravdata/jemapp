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
 * Control unit for product export process
 * 
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Model_Unit_Control extends Nostress_Nscexport_Model_Unit 
{    
	const MB_SIZE = 1024;
	const TIME = 'time';
	const MEMORY = 'memory';
	const PRODUCTS = 'products';
	const CATEGORIES = 'categories';
	const TIME_DECIMAL = 2;
	
	protected $_startTime;
	protected $_totalTime;
	protected $_productCounter = 0;
	protected $_categoryCounter = 0;
	
    protected function init()
    {
        $this->initStartTime();
    	$this->resetProductCounter();
        return $this;    
    }
    
    public function run($profile)
    {          
 		$this->canRun($profile);   	
    	
        $this->init();    	                          
        $profile->setStatus(self::STATUS_RUNNING);
        //load requested parameters from transform uint -- all
        $loaderProduct = Mage::getSingleton('nscexport/data_loader_product');       
        $xmlTransformator = Mage::getSingleton('nscexport/data_transformation_xml');
        $xsltTransformator = Mage::getSingleton('nscexport/data_transformation_xslt');
        $writer = Mage::getSingleton("nscexport/data_writer");      
        $writer->setData($profile->getWriterParams());  
                
        try 
        {
            $loaderProduct->init($profile->getLoaderParams());            
            $xmlTransformator->init($profile->getXmlTransformParams());
            $xsltTransformator->init($profile->getXsltTransformParams());
            
            //Export categories from DB
            if($profile->exportCategoryTree())
            {
            	$loaderCategory = Mage::getSingleton('nscexport/data_loader_category');
            	$loaderCategory->init($profile->getLoaderParams());
            	$categories = $loaderCategory->loadAll();
            	$xmlTransformator->insertCategories($categories);
            	$this->incrementCategoryCounter(count($categories));
            }
            
            //Export products from DB
            if($profile->exportProducts())
            {           	            	            
	            while(($productsNumber = count($batch = $loaderProduct->loadBatch())) > 0)
	            {
	                $this->incrementProductCounter($productsNumber);
	                $xmlTransformator->transform($batch);
	            }
	            
	            if($this->getProductCounter() == 0)
            		$this->logAndException("10");
            	$this->incrementProductCounter(-$xmlTransformator->getSkippedProductsCounter());
            }
                                            
           $xml = $xmlTransformator->getResult(true);
//           $writer->saveData($xml);                                                        
           $xsltTransformator->transform($xml);
           $result = $xsltTransformator->getResult();
           $writer->saveData($result);
          
            $this->stopTime();
        }
        catch(Exception $e)
        {
        	//extract error code from message
        	$this->processErrorMessage($profile,$e);
            throw $e;
        }
        
        $profile->resetUrl();
        $profile->setMessage($this->getProcessInfo(true));
        $profile->setStatus(self::STATUS_FINISHED);  
        $this->logEvent($profile->getFeed(),$profile->getUrl());      
        return $this;
    }
    
    //extract error code from message
    protected function processErrorMessage($profile,$exception)
    {
    	//extract error code from message
    	$error = $this->helper()->processErrorMessage($exception->getMessage());
    	$message = $error['message'];
    	
    	if(!empty($error['action_message']))
    	{
    		$tmpMes = '<br>'.str_replace("{{action_link}}", $error['action_link'],$error['action_message']);
    		if(!empty($error['params']))
    		{
    			$tmpMes = str_replace("{{params}}", $error['params'] ,$tmpMes);
    		}    		
    		$message .= $tmpMes;
    	}
    	
    	if(!empty($error['link']))
    		$message .= "<br>".$this->helper()->__('The solution for this problem is described in <a href="%s" target="_blank">Koongo Docs</a>.', $error['link']);
    	
    	$profile->setMessageStatusError($message,self::STATUS_ERROR);
    }
    
    public function getProcessInfo($format = true)
    {
    	$info = array();
    	$info[self::TIME] = $this->getTotalTime($format);
    	$info[self::MEMORY] = $this->getTotalMemory($format);
    	$info[self::PRODUCTS] = $this->getProductCounter();
    	$info[self::CATEGORIES] = $this->getCategoryCounter();
    	
    	if($format)
    	{
    		$infoString = "";
    		if($info[self::PRODUCTS] > 0)
    			$infoString = $this->helper()->__("Products: %s ",$info[self::PRODUCTS]);
    		if($info[self::CATEGORIES] > 0)
    			$infoString .= $this->helper()->__("Categories: %s ",$info[self::CATEGORIES]);
    	    $infoString .= $this->helper()->__("Time: %s Memory: %s ",$info[self::TIME],$info[self::MEMORY]);
    	    $info = $infoString;
    	}
    	
    	return $info;
    }
    
    protected function canRun($profile)
    {
    	$message = "";
    	if(is_null($profile->getFeedObject()))
 		{
 			$message = $this->helper()->__('Missing feed layout.'); 			 			
 		}     	    	
    	else if(!$profile->getEnabled())
	    {
	    	$message = $this->helper()->__('Profile # %s is disabled.',$profile->getId());	       	
	    }
	    
		if(!empty($message))
		{
			$profile->setMessageStatusError($this->helper()->__('Unable to generate an export').". ".$message,self::STATUS_ERROR);
		    throw new Exception($message);
		}
    }
    
    protected function initStartTime()
    {
   		$this->_startTime = $this->helper()->getProcessorTime();
    }
    
    protected function stopTime()
    {
    	$endTime =  $this->helper()->getProcessorTime();
    	$this->_totalTime = $endTime - $this->_startTime;
    }
    
    protected function getTotalTime($format = true)
    { 
    	$time = $this->_totalTime;
    	$time = round($time,self::TIME_DECIMAL);
    	if($format)
    		$time .= " ".$this->helper()->__("s");  
    	return $time;
    }
    
    protected function getTotalMemory($format = true)
    {
    	//$memory = memory_get_usage(true);
    	$memory = memory_get_peak_usage(1);
    	$memory = ($memory/self::MB_SIZE)/self::MB_SIZE;
    	if($format)
    		$memory .= " ".$this->helper()->__("MB");  
    	return $memory;
    }
    
    protected function incrementProductCounter($number)
    {
    	$this->_productCounter += $number;
    }
    
    protected function incrementCategoryCounter($number)
	{
    	$this->_categoryCounter += $number;
    }
    
    protected function resetProductCounter()
    {
    	$this->_productCounter = 0;
    }
    
    protected function getProductCounter()
    {
    	return $this->_productCounter;
    }
    
    protected function getCategoryCounter()
    {
    	return $this->_categoryCounter;
    }
    
    protected function logEvent($feedCode,$url)
    {
    	$info = $this->getProcessInfo(false);    	
    	$this->helper()->logRunProfileEvent($feedCode,$info[self::PRODUCTS],$info[self::CATEGORIES],$url);
    }
}