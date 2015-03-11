<?php

class WSC_MageJam_Model_Catalog_Layer_Api extends Mage_Catalog_Model_Category_Api
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    /**
     * Used for api method getLayer
     *
     * @param $categoryId
     * @param $appliedFilters
     * @param $complex_filters
     * @param $store
     * @return array
     */
    public function getLayerFilter($categoryId, $appliedFilters = array(), $complex_filters = array(), $pageNumber = 0, $pageSize = null, $sortBy = 'relevance', $sortDirection = 'asc', $store = null)
    {

        /* @var $layerHelper WSC_MageJam_Helper_Layer */
        $layerHelper = Mage::helper('magejam/layer');
        $layerHelper->setFilters($appliedFilters);

        $storeId = Mage::helper('magejam')->getStoreId($store);
        Mage::app()->setCurrentStore($storeId);

        /* @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);
        $category->load($categoryId);
        if(!$category->getId()) {
            $this->_fault('not_exist');
        }

        /* @var $layer Mage_Catalog_Model_Layer */
        $layer = Mage::getSingleton('catalog/layer');
        $layer->setCurrentCategory($category);
        $layerHelper->applyFilters($layer);

        $result['layer_filters'] = $layerHelper->getFilters($layer);

        $collection = $layer->getProductCollection();
        $collection = $this->_filterCollection($collection, $complex_filters);
        if(is_null($pageSize)) {
            $pageSize = Mage::helper('magejam/product')->getProductLimit();
        }
        $collection->setPage($pageNumber, $pageSize);
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSort($sortBy, $sortDirection);

        /* @var $productHelper WSC_MageJam_Helper_Product */
        $productHelper = Mage::helper('magejam/product');
        $result['products'] = $productHelper->convertProductCollectionToApiResponse($collection);

        if (!$category->getIsAnchor()){
            $currentChildCategories = $category->getChildrenCategories();
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $layer->prepareProductCollection($productCollection);
            $productCollection->addCountToCategories($currentChildCategories);

            $categoryFilters = array();
            $categoryFilter = array();
            $categoryFilter['name'] = 'Category';
            $categoryFilter['code'] = 'cat';
            $categoryFilter['items'] = array();
            foreach ($currentChildCategories as $childCategory){
                $item = array();
                $item['label'] = $childCategory->getName();
                $item['value'] = $childCategory->getId();
                $item['count'] = $childCategory->getProductCount();
                $categoryFilter['items'][] = $item;
            }
            $categoryFilters[] = $categoryFilter;
            $result['layer_filters'] = $categoryFilters;
        }
        return $result;
    }

    /**
     * Used for filtering collection (layer navigation for example)
     *
     * @param $collection
     * @param $filters
     * @return mixed
     */
    protected function _filterCollection(Mage_Catalog_Model_Resource_Product_Collection $collection, $filters)
    {
        if (empty($filters)) {
            return $collection;
        }
        /* @var $apiHelper WSC_MageJam_Helper_Api */
        $apiHelper = Mage::helper('magejam/api');
        $filters = $apiHelper->parseComplexFilters($filters, $this->_filtersMap);
        foreach ($filters as $field => $value) {
            $this->checkAttributeExistence($field, $collection);
            $collection->addFieldToFilter($field, $value);
        }
        return $collection;
    }

    /**
     * Check if attribute exists and throws exception if it doesn't
     *
     * @param $code
     * @param $collection
     */
    protected function checkAttributeExistence($code, $collection) {
        if(!$collection->getEntity()->getAttribute($code)) {
            $message = "Incorrect filter with code '{$code}' has been provided";
            $this->_fault('filters_invalid', $message);
        }
    }

}