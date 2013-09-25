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
* Model for Export
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Mysql4_Records extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the export_id refers to the key field in your database table.
        $this->_init('nscexport/records', 'entity_id');
    }
    
    public function insertRecords($profileId,$relationIds)
    {
    	$queary = "";
    	foreach($relationIds as $relationId)
    	{
    		$queary .= $this->insertRecordQuery($profileId,$relationId);
    	}
    	$this->runQuery($queary);
    }
    
    public function deleteRecords($entityIds)
    {
    	$queary = "";
    	foreach($entityIds as $entityId)
    	{	
    		$queary .= $this->deleteRecordQuery($entityId);
    	}
    	$this->runQuery($queary);
    }
    
    public function recordWithProfileIdExists($profileId)
    {
    	$q = "SELECT 1 FROM ".$this->getMainTable()." WHERE export_id = ".$profileId." ;";
    	$readresult = $this->runSelectQuery($q);

		if($readresult->fetch()) 
		{
			return true;
		}
		return false;
    }
    
    
    private function deleteRecordQuery($entityId)
    {
    	return "DELETE FROM ".$this->getMainTable()." WHERE ".$this->getMainTable().".entity_id = ".$entityId.";";    	
    }
    
    private function insertRecordQuery($profileId,$relationId)
    {
    	return "INSERT INTO ".$this->getMainTable()." (export_id,relation_id) VALUES (".$profileId.",".$relationId.");";  	
    }
    
    private function runQuery($queryString)
    {
    	if(!isset($queryString) || $queryString == "")
    		return $this;
    	$this->beginTransaction();
        try {
            
            $this->_getWriteAdapter()->query($queryString);       
            $this->commit();
        }
        catch (Exception $e){
            $this->rollBack();
            throw $e;
        }
        return $this;
    }
    
    private function runSelectQuery($queryString)
    {
    	return $this->_getWriteAdapter()->query($queryString);
    }
}