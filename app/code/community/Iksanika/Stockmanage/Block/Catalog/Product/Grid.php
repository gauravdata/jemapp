<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Stockmanage
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Stockmanage_Block_Catalog_Product_Grid extends Iksanika_Stockmanage_Block_Widget_Grid
{
    protected static $columnType = array(
        'id'                    =>  array('type'=>'number'),
        'product'               =>  array('type'=>'checkbox'),
        'name'                  =>  array('type'=>'text'),
        'custom_name'           =>  array('index'=>'custom_name'),
        'type_id'               =>  array('type'=>'text'),
        'attribute_set_id'      =>  array('type'=>'text'),
        'category_ids'          =>  array('type'=>'text'),
        'sku'                   =>  array('type'=>'text'),
        'price'                 =>  array('type'=>'text'),
        'qty'                   =>  array('type'=>'input'),
        'is_in_stock'           =>  array('type'=>'options'),
        'visibility'            =>  array('type'=>'text'),
        'status'                =>  array('type'=>'text'),
        'websites'              =>  array('type'=>'text'),
    );
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->prepareDefaults();
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setTemplate('iksanika/stockmanage/catalog/product/grid.phtml');
        $this->setMassactionBlockName('stockmanage/widget_grid_massaction');
    }
    
    private function prepareDefaults() 
    {
        $this->setDefaultLimit(20);
        $this->setDefaultPage(1);
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }
    
    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) 
        {
            if (
                isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()) 
            {
                $column->getFilter()->setValue($data[$columnId]);
                if($columnId != 'category_ids')
                    $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }
    
    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            $columnIndex = ($columnIndex == 'category_ids') ? 'cat_ids' : $columnIndex;
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }
    
    public function getQuery() 
    {
        return urldecode($this->getParam('q'));
    }
    
    protected function _prepareCollection()
    {
        
        $collection = $this->getCollection();
        $collection = !$collection ? Mage::getModel('catalog/product')->getCollection() : $collection;
        
        if($queryString = $this->getQuery()) 
        {
            $query = Mage::helper('catalogSearch')->getQuery();
            $query->setStoreId(Mage::app()->getStore()->getId());
            $query->setQueryText(Mage::helper('catalogsearch')->getQuery()->getQueryText());

            $collection = $query->getSearchCollection();
            $collection->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText());
        }

        $store = $this->_getStore();
        $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                   ->joinField('is_in_stock', 'cataloginventory/stock_item', 'is_in_stock', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                   ->joinField('cat_ids', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left');
        
        $collection->groupByAttribute('entity_id');

        if ($store->getId())
        {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        
        foreach(self::$columnType as $col => $true) 
        {
            if($col == 'category_ids')
            {
                //$filter = $this->getParam('filter');
//                echo $this->getVarNameFilter().'~';
                $filter = $this->getParam($this->getVarNameFilter());
                if($filter)
                {
                    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
                    if(isset($filter_data['category_ids']))
                    {
                        if(trim($filter_data['category_ids'])=='')
                            continue;
                        $categoryIds = explode(',', $filter_data['category_ids']);
                        $catIdsArray = array();
                        foreach($categoryIds as $categoryId)
                        {
                            //$collection->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));
                            $catIdsArray[] = $categoryId;
                        }
                        $collection->addAttributeToFilter('cat_ids', array( 'in' => $catIdsArray));                        
                        //$collection->printLogQuery(true);
                    }
                }
            }
            if($col == 'qty' || $col == 'websites' || $col=='id' || $col=='category_ids') 
                continue;
            else
                $collection->addAttributeToSelect($col);
        }

        $this->setCollection($collection);
        
        parent::_prepareCollection();

        $collection->addWebsiteNamesToResult();

        return $this;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();
        
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'name' => 'pu_name[]',
                'index' => 'name'/*,
                'width' => '150px'*/
        ));
        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                    'index' => 'custom_name',
                    'width' => '150px'
            ));
        }
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
        ));
        
        $this->addColumn('category_ids',
            array(
                'header'=> Mage::helper('catalog')->__('Category ID\'s'),
                'width' => '80px',
                'index' => 'category_ids',
                'name' => 'pu_category_ids[]',
                'type' => 'text'
        ));
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
                'name' => 'pu_sku[]',
        ));
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'name' => 'pu_price[]',
        ));
        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type'  => 'input',
                'index' => 'qty',
                'name' => 'pu_qty[]',
                'renderer' => 'Iksanika_Stockmanage_Block_Widget_Grid_Column_Renderer_Number',
        ));
        $this->addColumn('is_in_stock',
            array(
                'header'=> Mage::helper('catalog')->__('Is in Stock'),
                'width' => '100px',
                'type' => 'options', 
                'index' => 'is_in_stock',
                'name' => 'is_in_stock',
                'options' => array(0 => __('No'), 1 => __('Yes')),
                'renderer' => 'adminhtml/widget_grid_column_renderer_select',
        ));
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'id' => "editlink",
                        'url'     => array(
                            'base'=>'adminhtml/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        $this->setDestElementId('edit_form');
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current' => true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('attributes', 
            array(
                'label' => Mage::helper('catalog')->__('Update attributes'),
                'url'   => $this->getUrl('adminhtml/catalog_product_action_attribute/edit', array('_current'=>true))
            )
        );

        $this->getMassactionBlock()->addItem('otherDivider', $this->getDivider("Other"));
        
        /*
         * Prepare list of column for update
         */
        
        $this->getMassactionBlock()->addItem('save', 
            array(
                'label' => Mage::helper('catalog')->__('Update'),
                'url'   => $this->getUrl('*/*/massUpdateProducts'),
                'fields' => array(0=>'product', 1=>'qty', 2=> 'is_in_stock')
            )
        );
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function getDivider($divider="*******") 
    {
        $dividerTemplate = array(
            'label' => '********'.$this->__($divider).'********',
            'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
            'callback' => "null"
        );
        return $dividerTemplate;
    }
    
}