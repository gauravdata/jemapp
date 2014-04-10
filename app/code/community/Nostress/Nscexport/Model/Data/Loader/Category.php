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
* Product loader for export process
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Data_Loader_Category extends Nostress_Nscexport_Model_Data_Loader
{       
    
    public function _construct()
    {    
        // Note that the export_id refers to the key field in your database table.
        $this->_init('nscexport/data_loader_category', 'entity_id');
    }	
    
    public function initAdapter()
    {
        parent::initAdapter();                
        $this->basePart();
        //echo $this->adapter->getSelect()->__toString();
        //exit();
    } 
  
    
    //***************************BASE PART**************************************    
    protected function basePart()
    {
    	$this->adapter->joinParentCategory();
    	$this->adapter->joinCategoryUrlRewrite();
        $this->adapter->joinCategoryPath();
    	$this->adapter->orderByLevel();
        $filterByProducts = $this->getUseProductFilter();
    	
//    	if($filterByProducts)
//    	{
//    		$this->categoryFilterByProducts();
//    	}
    }
    
    protected function categoryFilterByProducts()
    {
    	$this->adapter->joinProductFilter ();    	
    }
    
    //***************************COMMON PART**************************************
    
    protected function commonPart()
    {
    	
    } 
}
?>