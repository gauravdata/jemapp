<?php

class WSC_MageJam_Helper_Layer extends Mage_Core_Helper_Abstract
{
    /**
     * Converts Mage_Catalog_Model_Layer_Filter_Abstract into array
     *
     * @param $filter
     * @return array
     */
    protected function _filterToArray(Mage_Catalog_Model_Layer_Filter_Abstract $filter)
    {
        $result = array();
        $result['name'] = $filter->getName();
        $result['code'] = (string) $filter->getRequestVar();
        foreach($filter->getItems() as $item) {
            $result['items'][] = $this->_itemToArray($item);
        }

        return $result;
    }

    /**
     * Converts array Mage_Catalog_Model_Layer_Filter_Item into array
     *
     * @param $item
     * @return array
     */
    protected function _itemToArray($item)
    {
        $result = array();
        $result['count'] = (int) $item->getCount();
        $result['label'] = (string) $item->getLabel();
        $result['value'] = (string) $item->getValue();

        return $result;
    }

    public function getFilters(Mage_Catalog_Model_Layer $layer)
    {
        $result = array();
        foreach($layer->getFilters() as $filter ) {
            if($filter->getItemsCount()) {
                $result[] = $this->_filterToArray($filter);;
            }
        }

        return $result;
    }

    public function applyFilters(Mage_Catalog_Model_Layer $layer)
    {
        /* @var $categoryFilter Mage_Catalog_Model_Layer_Filter_Category */
        $categoryFilter = Mage::getSingleton('catalog/layer_filter_category');
        $categoryFilter->setLayer($layer);
        $layer->getState()->setFilters(array());
        $categoryFilter->apply(Mage::app()->getRequest(), null);
        $filters = array($categoryFilter);

        $filterableAttributes = $layer->getFilterableAttributes();

        foreach ($filterableAttributes as $attribute) {
            /* @var $attributeFilter Mage_Catalog_Model_Layer_Filter_Abstract */
            $attributeFilter = null;

            if ($attribute->getAttributeCode() == 'price') {
                $attributeFilter = Mage::getModel('magejam/catalog_layer_filter_price');
            } elseif ($attribute->getBackendType() == 'decimal') {
                $attributeFilter = Mage::getModel('catalog/layer_filter_decimal');
            } else {
                if($layer instanceof Mage_Catalogsearch_Model_Layer) {
                    $attributeFilter = Mage::getModel('catalogsearch/layer_filter_attribute');
                } else {
                    $attributeFilter = Mage::getModel('catalog/layer_filter_attribute');
                }
            }

            $attributeFilter->setAttributeModel($attribute);
            $attributeFilter->setLayer($layer);
            $attributeFilter->apply(Mage::app()->getRequest(), null);
            $filters[] = $attributeFilter;
        }

        $layer->setFilters($filters);
    }

    /**
     * Sets filters in request as params
     *
     * @param $filters
     */
    public function setFilters($filters)
    {
        $request = Mage::app()->getRequest();
        foreach($filters as $key => $value) {
            $request->setParam($key, $value);
        }
    }
}

