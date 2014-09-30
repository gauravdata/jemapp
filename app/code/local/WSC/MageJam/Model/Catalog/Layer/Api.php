<?php

class WSC_MageJam_Model_Catalog_Layer_Api extends Mage_Catalog_Model_Category_Api
{
    /**
     * Used for api method getLayer
     *
     * @param $categoryId
     * @param $appliedFilters
     * @param $store
     * @return array
     */
    public function getLayerFilter($categoryId, $appliedFilters = array(), $store = null)
    {
        $appliedFilters['cat'] = $categoryId;
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

        return $layerHelper->getFilters($layer);
    }

}