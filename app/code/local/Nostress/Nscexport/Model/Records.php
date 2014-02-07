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

class Nostress_Nscexport_Model_Records extends Mage_Core_Model_Abstract 
{
	protected $_store;
	protected $_batchSize;
	
	public function _construct() 
	{
		parent::_construct ();
		$this->_init ( 'nscexport/records' );
	}
	
	public function setBatchSize($batchSize)
	{
		$this->_batchSize = $batchSize;
	}	
	
	public function saveRelations($profileId,$relationIds)
	{
		try 
	    {
            $this->getResource()->insertRecords($profileId,$relationIds);
		} 
		catch (Exception $e) 
		{
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage()
                //Mage::helper('catalogrule')->__('Unable to apply rules.')
            );
            throw $e;
	    }
	}
	
	public function deleteRecords($entityIds)
	{
    	if(!is_array($entityIds))
    		$entityIds = array($entityIds);
		try 
	    {
            $this->getResource()->deleteRecords($entityIds);
		} 
		catch (Exception $e) 
		{
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage()
                //Mage::helper('catalogrule')->__('Unable to apply rules.')
            );
            throw $e;
	    }
	}
	
	public function profileHasRecordsToExport($profileId)
	{
		return $this->getResource()->recordWithProfileIdExists($profileId);
	}
	
	public function getRecordsCount($profileId)
	{
		return count($this->getCollection()->addFieldToFilter('export_id',$profileId)->load());
	}
	
	public function getExportBatch($profileId)
	{
		$collection = $this->getCollection();
		$collection->joinExportRelationsTable()->addLimit($this->_batchSize,0)->addWhereCondition($profileId);				
		//$aa = $collection->getSelectSql()->__toString();
		$collection->load();
		return $this->prepareBatchArray($collection);
	}
	
	private function prepareBatchArray($collection)
	{
		$result = array();
		foreach($collection as $item)
		{
			$result[$item->getProductId()] = array("categoryId" => $item->getCategoryId(), "recordId" => $item->getId());
		}	
		return $result;	
	}
}