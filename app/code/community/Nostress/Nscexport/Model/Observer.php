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
 * Observer for Export
 * 
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Model_Observer extends Nostress_Nscexport_Model_Abstract
{	
	const ADD_PRODUCT_TO_PROFILE_FLAG = "nostress_add_product_to_profile";
	
	protected $_rulePrices = array();
	protected $_srcModel;

	
	protected function getSourceModel()
	{
		if(!isset($this->_srcModel))
			$this->_srcModel = Mage::getModel('nscexport/profile');
		return $this->_srcModel;
	}
	
	/**
     * Handles a custom AfterSave event for Catalog Products
     * Determines if the product is new.
     * Calls function for adding product to proper profile.
     *
     * @param array $eventArgs
     */
    public function processCatalogProductAfterSaveEvent($eventArgs)
    {    	
		//Pull the product out of the EventArgs parameter
        $product = $eventArgs['data_object'];     
        $flag = Mage::registry(self::ADD_PRODUCT_TO_PROFILE_FLAG);   
        if(isset($flag) && $flag == true)
        {
        	Mage::unregister(self::ADD_PRODUCT_TO_PROFILE_FLAG);
         	Mage::getModel('nscexport/categoryproducts')->updateProductAssignment($product);	
        }
   	}
   	
   	public function setNewProductSaveFlag($eventArgs)
   	{
   	 	$product = $eventArgs['data_object'];
        if ($this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_ADD_PRODUCTS) == 1)
        {
        	$regValue = Mage::registry(self::ADD_PRODUCT_TO_PROFILE_FLAG);
        	if(isset($regValue))
    			Mage::unregister(self::ADD_PRODUCT_TO_PROFILE_FLAG);
         	Mage::register(self::ADD_PRODUCT_TO_PROFILE_FLAG,true);
        }
   	}   
    
	/**
     * Generate Koongo Connector scheduled profiles
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function generateFeeds($schedule)
    {   
    	$profileIds = Mage::getModel('nscexport/cron')->getScheduledProfiles();
    	if(empty($profileIds))
    		return;
    	
    	$this->helper()->runProfilesByIds($profileIds);  			       		 	    		    	                                              
    }
    
    public function updatePluginInfo($schedule)
    {
        Mage::helper('nscexport/data_client')->updatePlugins();
        Mage::helper('nscexport/data_client')->updateLicense();
    }
    
    protected function helper()
    {
    	return Mage::helper('nscexport/data_profile');
    }
}