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
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* @category Nostress 
* @package Nostress_Nscexport
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Abstract extends Mage_Adminhtml_Block_Template
{
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	* Retrieve current profile instance
	*
	* @return Nostress_Nscexport_Profile
	*/
	public function getProfile() {
		return Mage::registry('nscexport_profile');
	}
	
	public function getProfileId() {
		if ($this->getProfile()) {
			return $this->getProfile()->getId();
		}
		//return Mage_Catalog_Model_Category::TREE_ROOT_ID;
		return 0;
	}
	
	public function getCategoryName() {
		return $this->getCategory()->getName();
	}
	
	public function getCategoryPath() {
		if ($this->getCategory()) {
			return $this->getCategory()->getPath();
		}
		return Mage_Catalog_Model_Category::TREE_ROOT_ID;
	}
	
	public function hasStoreRootCategory() {
		$root = $this->getRoot();
		if ($root && $root->getId()) {
			return true;
		}
		return false;
	}
	
	public function getStore() {
		$storeId = (int) $this->getRequest()->getParam('store');
		return Mage::app()->getStore($storeId);
	}
	
	public function getRoot($parentNodeCategory = null, $recursionLevel = 3) {
		if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
			return $this->getNode($parentNodeCategory, $recursionLevel);
		}
		$root = Mage::registry('root');
		if (is_null($root)) {
			$storeId = (int) $this->getRequest()->getParam('store');
			
			if ($storeId) {
				$store = Mage::app()->getStore($storeId);
				$rootId = $store->getRootCategoryId();
			}
			else {
				$rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
			}
			
			$tree = Mage::getResourceSingleton('catalog/category_tree')
				->load(null, $recursionLevel);
			
			if ($this->getCategory()) {
				$tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
			}
			
			$tree->addCollectionData($this->getCategoryCollection());
			
			$root = $tree->getNodeById($rootId);
			
			if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
				$root->setIsVisible(true);
			}
			elseif($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
				$root->setName(Mage::helper('catalog')->__('Root'));
			}
			
			Mage::register('root', $root);
		}
		
		return $root;
	}
	
    /**
     * Get and register categories root by specified categories IDs
     *
     * IDs can be arbitrary set of any categories ids.
     * Tree with minimal required nodes (all parents and neighbours) will be built.
     * If ids are empty, default tree with depth = 2 will be returned.
     *
     * @param array $ids
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $categoryTreeResource = Mage::getResourceSingleton('catalog/category_tree');
            $ids    = $categoryTreeResource->getExistingCategoryIdsBySpecifiedIds($ids);
            $tree   = $categoryTreeResource->loadByIds($ids);
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
            } else if($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            $tree->addCollectionData($this->getCategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    public function getNode($parentNodeCategory, $recursionLevel=2)
    {
        $tree = Mage::getResourceModel('catalog/category_tree');

        $nodeId     = $parentNodeCategory->getId();
        $parentId   = $parentNodeCategory->getParentId();

        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setIsVisible(true);
        } elseif($node && $node->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setName(Mage::helper('catalog')->__('Root'));
        }

        $tree->addCollectionData($this->getCategoryCollection());

        return $node;
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function getEditUrl()
    {
        return $this->getUrl("*/catalog_category/edit", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }

    /**
     * Return ids of root categories as array
     *
     * @return array
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if (is_null($ids)) {
            $ids = array();
            foreach (Mage::app()->getGroups() as $store) {
                $ids[] = $store->getRootCategoryId();
            }
            $this->setData('root_ids', $ids);
        }
        return $ids;
    }
}
