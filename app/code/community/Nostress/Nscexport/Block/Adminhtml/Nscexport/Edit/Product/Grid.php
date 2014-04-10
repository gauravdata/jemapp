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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_allIds = "";
	protected $_store = null;
	protected $_category = null;
	
	public function __construct() {
		parent::__construct();
		$this->setId('nscexportCatalogCategoryProducts');
		$this->setDefaultSort('entity_id');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setCheckboxesName('product');
	}

    public function manualInit() {
        $this->setId('nscexportCatalogCategoryProducts');
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setCheckboxesName('categoryProducts');
    }
	
	protected function _getStore() {
		if (!isset($this->_store) || $this->_store == null) {
			$this->_store = Mage::app()->getStore($this->getData("store"));
		}
		return $this->_store;
	}
	
	protected function _getCategory() {
		if (!isset($this->_category) || $this->_category == null) {
			$categoryId = $this->getData("category");
			$this->_category = Mage::getModel('catalog/category')->load($categoryId);
		}
		return $this->_category;
	}
	
	protected function _addColumnFilterToCollection($column) {
		// Set custom filter for in category flag
		if ($column->getId() == 'in_category') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
			}
			elseif(!empty($productIds)) {
				$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}
	
	protected function _prepareCollection() {
		$store = $this->_getStore();
		$categoryId = $this->_getCategory()->getId();
		
		if (!isset($categoryId))
			$categoryId = 0;
	 
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addStoreFilter($store)
            ->joinField('position',
                'catalog/category_product',
                'position',
                'product_id=entity_id',
                'category_id='.(int)$categoryId,
                'inner')
            ->joinField('qty',
				'cataloginventory/stock_item',
				'qty',
				'product_id=entity_id',
				null,
				'left');
        
		if ($store->getId()) {
			$collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
		}
		else {
			$collection->addAttributeToSelect('status');
			$collection->addAttributeToSelect('visibility');
		}

        $this->setCollection($collection);	

		parent::_prepareCollection();
		$this->getCollection()->addWebsiteNamesToResult();
		$this->_allIds = $this->getCollection()->getAllIds();
		
		return $this;
	}
	
	protected function _prepareColumns() {
		$this->addColumn('chosen_product', array(
			'header_css_class' => 'a-center',
			'type' => 'checkbox',
			'name' => 'chosen_product[]',
			'values' => array(), //$this->_getSelectedProducts(),
			'align' => 'center',
			'index' => 'entity_id'
		));
		
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('catalog')->__('ID'),
			'sortable'  => true,
			'type'  => 'number',
			'index'     => 'entity_id'
		));
		$this->addColumn('name', array(
			'header'    => Mage::helper('catalog')->__('Name'),
			'index'     => 'name'
		));
		
		$this->addColumn('type',
			array(
				'header'=> Mage::helper('catalog')->__('Type'),
				'index' => 'type_id',
				'type'  => 'options',
				'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
		));
		
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
			->load()
			->toOptionHash();
		
		$this->addColumn('set_name',
			array(
				'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
				'index' => 'attribute_set_id',
				'type'  => 'options',
				'options' => $sets,
		));
		
		$this->addColumn('sku',
			array(
				'header'=> Mage::helper('catalog')->__('SKU'),
				'index' => 'sku',
		));
		
		$this->addColumn('qty',
			array(
				'header'=> Mage::helper('catalog')->__('Qty'),
				'type'  => 'number',
				'index' => 'qty',
		));
		
		$this->addColumn('status',
			array(
				'header'=> Mage::helper('catalog')->__('Status'),
				'index' => 'status',
				'type'  => 'options',
				'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
		));
		
		$this->addColumn('visibility',
			array(
				'header'=> Mage::helper('catalog')->__('Visibility'),
				'index' => 'visibility',
				'type'  => 'options',
				'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
		));
		
		return parent::_prepareColumns();
	}
	
	public function getGridUrl() {
		return $this->getUrl('*/*/productGrid', array('_current'=>true));
	}
	
	protected function _getSelectedProducts() {
		$products = $this->getRequest()->getPost('selected_products');
		if (is_null($products)) {
			$products = $this->_getCategory()->getProductsPosition();
			return array_keys($products);
		}
		return $products;
	}
	
	public function getGridIdsJson() {
		$gridIds = $this->_allIds;
		
		if (!empty($gridIds)) {
			return join(",", $gridIds);
			//return Mage::helper('core')->jsonEncode($gridIds);
		}
		return '';
	}
	
	public function getChosenProductsHtml() {
		return '
		<span class="field-row">
			<input type="hidden" name="chosen_product_ids" id="chosen_product_ids" value="'.$this->getGridIdsJson().'">
			<div id="chosen_product_ids"></div>
		</span>';
	}
	
	public function getRowClickCallback() {
		return "openGridRow";
	}
}