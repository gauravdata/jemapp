<?php

class WSC_MageJam_Model_Catalogsearch_Api extends Mage_Catalog_Model_Category_Api
{
    /**
     * Used for api call catalogSearch
     *
     * @param $query
     * @param array $filters
     * @param int $pageNumber
     * @param null $pageSize
     * @param null $store
     * @param bool $useLayerNavigation
     * @return mixed
     */
    public function products($query, $filters = array(), $pageNumber = 0, $pageSize = null, $store = null, $useLayerNavigation = true)
    {
        /* @var $layerHelper WSC_MageJam_Helper_Layer */
        $layerHelper = Mage::helper('magejam/layer');
        $layerHelper->setFilters($filters);
        $storeId = Mage::helper('magejam')->getStoreId($store);
        Mage::app()->setCurrentStore($storeId);

        $this->checkQuery($query);
        Mage::app()->getRequest()->setParam(Mage_CatalogSearch_Helper_Data::QUERY_VAR_NAME, $query);

        /* @var $searchHelper Mage_CatalogSearch_Helper_Data */
        $searchHelper = Mage::helper('catalogsearch');
        $searchHelper->getQuery()->prepare();

        /* @var $layer Mage_Catalogsearch_Model_Layer */
        $layer = Mage::getSingleton('catalogsearch/layer');
        $layer->apply();


        $layerHelper->applyFilters($layer);

        if ($useLayerNavigation) {
            $result['layer_filters'] = $layerHelper->getFilters($layer);
        }

        $collection = $layer->getProductCollection();
        if(is_null($pageSize)) {
            $pageSize = Mage::helper('magejam/product')->getProductLimit();
        }
        $collection->setPage($pageNumber, $pageSize);
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToFilter('send_to_jmango', 1);

        /* @var $productHelper WSC_MageJam_Helper_Product */
        $productHelper = Mage::helper('magejam/product');
        $result['products'] = $productHelper->convertProductCollectionToApiResponse($collection);

        return $result;
    }

    /**
     * Used for checking query
     *
     * @param $query
     */
    public function checkQuery($query)
    {
        /* @var $searchHelper Mage_CatalogSearch_Helper_Data */
        $searchHelper = Mage::helper('catalogsearch');
        /* @var $stringHelper Mage_Core_Helper_String */
        $stringHelper = Mage::helper('core/string');

        $thisQueryLength = $stringHelper->strlen($query);
        $minQueryLength = $searchHelper->getMinQueryLength();
        if(!$thisQueryLength || $minQueryLength !== '' && $thisQueryLength < $minQueryLength ) {
            $message = Mage::helper('catalogsearch')->__('Minimum Search query length is %s', $this->_getQuery()->getMinQueryLength());
            $this->_fault('min_query_length', $message);
        }

        $maxQueryLength = $searchHelper->getMaxQueryLength();
        if ($maxQueryLength !== '' && $thisQueryLength > $maxQueryLength) {
            $message = Mage::helper('catalogsearch')->__('Maximum Search query length is %s', $this->getMaxQueryLength());
            $this->_fault('max_query_length', $message);
        }
    }
}