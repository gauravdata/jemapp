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
 * Model for search engines taxonomy
 * 
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Model_Entity_Attribute_Taxonomy extends Nostress_Nscexport_Model_Abstract 
{    
	const ATTRIBUTE_CODE = 'nsc_engine_taxonomy';
	const GROUP_NAME = 'Koongo Connector';
	const SOURCE_MODEL = 'nscexport/entity_attribute_source_taxonomy';
	const ENGINE_NAME_SUFFIX = ' Taxonomy';
	 
	const FIELD_CODE = 'code';
	const FIELD_NAME = 'name';
	
	const NULL_GROUP_ID = 0;
	
	protected $_setup;
	protected $_entityTypeId;
	
	public function prepareAttributes()
	{
		$attributes = $this->getAttributes();
		$sets = $this->helper()->getAttributeSets(Nostress_Nscexport_Helper_Data::ENTITY_CATEGORY);
		$entityTypeId = $this->getEntityTypeId();
		foreach ($sets as $set)
		{
			$setId = $set->getId();
			$groupId = $this->addAttributeGroup($set->getId(),self::GROUP_NAME);
			$this->addAttributes($attributes,$entityTypeId,$setId,$groupId);			
		}
		$this->closeSetup();
	}
	
	protected function addAttributeGroup($setId, $name, $sortOrder = null)
	{
		$setup = $this->getSetup();
		$setup->addAttributeGroup($this->getEntityTypeId(), $setId, $name, $sortOrder);
		$groupId = $setup->getAttributeGroup($this->getEntityTypeId(), $setId, self::GROUP_NAME, 'attribute_group_id');
		return $groupId;
	}
	
	protected function addAttributes($attributes,$entityTypeId,$attributeSetId,$attributeGroupId)
	{
		//original group Id
		$originalGroupId = $attributeGroupId;
		$enabled = Mage::getModel('nscexport/feed')->getEnabledTaxonomies();
		foreach($attributes as $attribute)
		{			
			$taxonomyCode = $attribute->getCode();
			if(!in_array($taxonomyCode,$enabled))
				$attributeGroupId = self::NULL_GROUP_ID;
			else 
				$attributeGroupId = $originalGroupId;
				
			$code = $this->helper()->createCategoryAttributeCode($attribute->getData(self::FIELD_CODE));
			$name = $attribute->getData(self::FIELD_NAME);
			$this->addAttribute($name,$code,$entityTypeId,$attributeSetId,$attributeGroupId);
		}
	}
	
	protected function addAttribute($name,$code,$entityTypeId,$attributeSetId,$attributeGroupId)
	{
		$setup = $this->getSetup();
		
		$setup->addAttribute('catalog_category', $code,  array(
							    'type'     => 'text',
							    'label'    => $name,
							    'input'    => 'select',
							    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
							    'visible'           => true,
							    'required'          => false,
							    'user_defined'      => false,
							    'default'           => "",
								'source'      =>  self::SOURCE_MODEL
							));
 
		$setup->addAttributeToGroup(
		    $entityTypeId,
		    $attributeSetId,
		    $attributeGroupId,
		    $code,
		    '0'                    //position
		);
	}
	
	protected function getSetup()
	{
		if(!isset($this->_setup))		
		{
			$this->_setup = Mage::getModel('eav/entity_setup','eav_setup');
			$this->_setup->startSetup();
		}
		return $this->_setup;
	}
	
	protected function closeSetup()
	{
		$setup = $this->getSetup();
		$setup->cleanCache();
		$setup->endSetup();
	}
	
	protected function getEntityTypeId()
	{
		if(!isset($this->_entityTypeId))
		{	
			$entity = $entity = $this->helper()->getEntityType(Nostress_Nscexport_Helper_Data::ENTITY_CATEGORY);
			$this->_entityTypeId = $entity->getId();
		}
		return $this->_entityTypeId;
	}
    
	protected function getAttributes()
	{
		$attributes = Mage::getModel('nscexport/taxonomy_setup')->getCollection();
		$attributes->addFieldToSelect(self::FIELD_CODE);
		$attributes->addFieldToSelect(self::FIELD_NAME);
		$attributes->getSelect();
		$attributes->load();
		return $attributes->getItems();
	}    
}