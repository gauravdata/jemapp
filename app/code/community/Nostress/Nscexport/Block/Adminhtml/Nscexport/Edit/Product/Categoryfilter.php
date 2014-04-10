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
 */

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit_Product_Categoryfilter extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
	const INDEX_HOURS = 0;
	const INDEX_MINUTES = 1;
	const INDEX_SECONDS = 2;
	const MAX_RECURSION_LEVEL = 100;

    protected $_store = null;
	protected $_root = null;
    protected $_selectedNodes = null;
    protected $_categoryProductsIds = null;
    protected $_categoryIds = null;

    public function __construct()
    {
        parent::__construct();
       // $this->setDestElementId('edit_form');
        $this->setTemplate('nscexport/categoryfilter.phtml');
    }
    
    public function initData()
    {
    	if(!$this->hasStartTime())
    		return;
    	$time = explode(Nostress_Nscexport_Helper_Data::TIME_DELIMITER,$this->getStartTime());    	
    	$this->setStartTimeHour($time[self::INDEX_HOURS]);
    	$this->setStartTimeMinute($time[self::INDEX_MINUTES]);
    	$this->setStartTimeSecond($time[self::INDEX_SECONDS]);
    }
    
    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) 
        {
        	//Nostress modif - get store id from param or from profile
        	$params = $this->getRequest()->getParams();
        	if(isset($params['store']))
        	{
        		$storeId = (int)$params['store'];
        		$store = Mage::app()->getStore($storeId);
        	}
        	else
        	{
        		$store = $this->getStore();        		
        	}

            if ($store)                     
                $rootId = $store->getRootCategoryId();            
            else 
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            

            //Nostress modif - misto rootId se pouzije parentId
			$absolutRootId = $rootId;
			$category = Mage::getModel('catalog/category')->load($rootId);
			if($category->hasParentId())
				$absolutRootId = $category->getParentId();
            
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->load($absolutRootId, $recursionLevel);

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
        
	public function getRootNode()
    {
    	$root = Mage::registry('root');
        if (is_null($root)) 
        {
    		$recursionLevel = self::MAX_RECURSION_LEVEL;  
    		$root = $this->getRoot(null,$recursionLevel);
        	if ($root && in_array($root->getId(), $this->getCategoryIds())) 
        	{
            	$root->setChecked(true);
        	}
        }
        return $root;
    }

    
    protected function getCategoryIds()
    {    
    	if($this->_categoryIds != null)
    		return $this->_categoryIds;
    	
    	$profileId = $this->getExportId();
    	if(isset($profileId))
    		$this->_categoryIds = Mage::getModel('nscexport/categoryproducts')->getExportCategories($profileId);
    	else
    		$this->_categoryIds = array();
    	return $this->_categoryIds;
    }   	

    protected function getCategoryProductsIds()
    {
    	if($this->_categoryProductsIds != null)
    		return $this->_categoryProductsIds;
    	
    	$profileId = $this->getExportId();
    	if(isset($profileId))
    		$this->_categoryProductsIds = Mage::getModel('nscexport/categoryproducts')->getExportCategoryProducts($profileId);
    	else
    		$this->_categoryProductsIds = "";
    	return $this->_categoryProductsIds;
    }

    public function getIdsString()
    {
        return $this->getData('category_ids');        
    }

 	public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('*/*/categoriesJson', array('_current'=>true));
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = $this->getNextNodeJson($node, $level);

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent) {
            $item['expanded'] = true;
        }    
       
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        return $item;
    }

	protected function getNextNodeJson($node, $level=0)
    {
        $item = array();
        $item['text']= $this->htmlEscape($node->getName());

        if ($this->_withProductCount) {
             $item['text'].= ' ('.$node->getProductCount().')';
        }

        //$rootForStores = Mage::getModel('core/store')->getCollection()->loadByCategoryIds(array($node->getEntityId()));
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());
        $item['id']  = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $item['allowDrop'] = true;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = ($node->getLevel()==1 && $rootForStores) ? false : true;

        if ($node->hasChildren()) {
            $item['children'] = array();

            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child, $level+1);
            }
        }
        return $item;
    }
    
    protected function _isParentSelectedCategory($node)
    {
        foreach ($this->_getSelectedNodes() as $selected) {
            if ($selected) {
                $pathIds = explode('/', $selected->getPathId());
                if (in_array($node->getId(), $pathIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            foreach ($this->getCategoryIds() as $categoryId) {
                $this->_selectedNodes[] = $this->getRoot()->getTree()->getNodeById($categoryId);                
            }
        }

        return $this->_selectedNodes;
    }

    public function getValue($key, $default='')
    {
        $value = $this->getData($key);
        return htmlspecialchars(strlen($value) > 0 ? $value : $default);
    }

    public function getSelected($key, $value)
    {        
        return $this->getData($key)==$value ? 'selected="selected"' : '';
    }

    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked="checked"' : '';
    }
    
    public function getStoreName($storeId)
    {
    	return Mage::app()->getStore($storeId)->getName();
    }
    
    public function getStore()
    {
    	if($this->_store)
    		return $this->_store;
    	$storeId = null;
    	$profileId = (int) $this->getRequest()->getParam('id');
    	if ($profileId != null)
    		$storeId = Mage::getModel('nscexport/profile')->load($profileId)->getData('store_id');
    	if(Mage::registry('nscexport_data'))
    	{
    		$storeId = Mage::registry('nscexport_data')->getStoreId();
    	}
    	if(!$storeId)
    		$storeId =$this->getValue('store_id');
    	if(!$storeId)
           	$storeId = (int)$this->getRequest()->getParam('store');
        $this->_store = Mage::app()->getStore($storeId);
        return $this->_store;
     }
            
    protected function _prepareLayout()
	{
		if(Mage::registry('category'))
			Mage::unregister('category');
		Mage::register('category',Mage::getModel('catalog/category')->load($this->getStore()->getRootCategoryId()));
		$block = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_grid', 'nscexportCatalogCategoryProducts');
    	$this->setChild('nscexportCatalogCategoryProducts',$block );    	
		return parent::_prepareLayout();
	}
	
	public function getAllGridIds()
	{
		return $this->getChild('nscexportCatalogCategoryProducts')->getGridIdsJson();
	}
	
	public function getCategoryGridHtml() {
		$form = $this->getData('form');
		$categories = $form->getParent()->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_grid')
			->setData(
				array(
					'category' => $this->getData('category'),
					'store' => $this->getData('store')
				));
		$categories->manualInit();
		return $categories->toHtml();
	}
	
}