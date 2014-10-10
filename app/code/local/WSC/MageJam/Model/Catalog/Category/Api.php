<?php

class WSC_MageJam_Model_Catalog_Category_Api extends Mage_Catalog_Model_Category_Api
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    /**
     * Retrieve list of assigned products to category
     *
     * @param $categoryId
     * @param $customerId
     * @param int $pageNumber
     * @param null $pageSize
     * @param null $filters
     * @param null $store
     * @return array
     */
    public function assignedProducts($categoryId, $filters = null, $customerId = null, $pageNumber = 0, $pageSize = null, $store = null)
    {
        $storeId = Mage::helper('magejam')->getStoreId($store);
        Mage::app()->setCurrentStore($storeId);
        $category = $this->_initCategory($categoryId, $storeId);

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $category->setStoreId($storeId)->getProductCollection();
        $collection = $this->_filterCollection($collection, $filters);

        if(is_null($pageSize)) {
            $pageSize = Mage::helper('magejam/product')->getProductLimit();
        }
        $collection->setPage($pageNumber, $pageSize);
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
        $collection->addAttributeToFilter('send_to_jmango', 1);
        ($storeId == 0)? $collection->addOrder('position', 'asc') : $collection->setOrder('position', 'asc');

        /* @var $productHelper WSC_MageJam_Helper_Product */
        $productHelper = Mage::helper('magejam/product');
        return $productHelper->convertProductCollectionToApiResponse($collection, $customerId);
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
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
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