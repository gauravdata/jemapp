<?php

class WSC_MageJam_Helper_Product extends Mage_Core_Helper_Abstract
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array('gallery', 'media_image');

    protected $_selectedAttributes = array(
        'sku',
        'name',
        'description',
        'short_description',
        'visibility',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date'
    );

    /**
     * Returns max possible amount of products according to memory limit settings
     * Copied from Mage_ImportExport_Model_Export_Entity_Product::export()
     *
     * @return int
     */
    public function getProductLimit() {

        $memoryLimit = trim(ini_get('memory_limit'));
        $lastMemoryLimitLetter = strtolower($memoryLimit[strlen($memoryLimit)-1]);
        switch($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
                break;
            case 'm':
                $memoryLimit *= 1024;
                break;
            case 'k':
                $memoryLimit *= 1024;
                break;
            default:
                // minimum memory required by Magento
                $memoryLimit = 250000000;
        }

        // Tested one product to have up to such size
        $memoryPerProduct = 100000;
        // Decrease memory limit to have supply
        $memoryUsagePercent = 0.8;
        // Minimum Products limit
        $minProductsLimit = 500;

        $productLimit = intval(($memoryLimit  * $memoryUsagePercent - memory_get_usage(true)) / $memoryPerProduct);
        if ($productLimit < $minProductsLimit) {
            $productLimit = $minProductsLimit;
        }

        return $productLimit;
    }

    public function convertProductCollectionToApiResponse(Mage_Catalog_Model_Resource_Product_Collection $collection, $customerId = null)
    {
        $this->disableFlat();
        $this->setCustomerGroupIntoSession($customerId);
        $collection->applyFrontendPriceLimitations();
        $collection->addAttributeToSelect($this->_selectedAttributes);
        $collection->addTierPriceData()->addOptionsToResult();
        $collection = Mage::helper('magejam/product_media')->addMediaGalleryAttributeToCollection($collection);

        $result = array();
        foreach ($collection as $product) {
            $result[] = $this->convertProductToApiResponse($product);
        }

        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        $session->clear();

        return $result;
    }

    /**
     * Setting customer group into session for correct applying catalog rule on further steps
     *
     * @param null $customerId
     */
    protected function setCustomerGroupIntoSession($customerId = null)
    {
        if(!$customerId) {
            return;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($customerId);

        if ($customer->isEmpty()) {
            return;
        }
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        $session->setCustomerGroupId($customer->getGroupId());
    }

    /**
     * Prepares Product info for api response
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function convertProductToApiResponse(Mage_Catalog_Model_Product $product)
    {
        Mage::dispatchEvent(
            'catalog_product_type_configurable_price',
            array('product' => $product)
        );

        $result = array(
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds(),
            'position'   => $product->getCatIndexPosition()
        );

        $this->_addMediaInfo($product, $result);
        $this->_addCustomOptions($product, $result);
        $this->_addConfigurableAttributes($product, $result);
        $this->_addGroupedItems($product, $result);
        $this->_addBundleInfo($product, $result);
        $this->_addDownloadableInfo($product, $result);

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute)) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                    $attribute->getAttributeCode());
            }
        }

        return $result;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addBundleInfo(Mage_Catalog_Model_Product $product, &$result)
    {
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return;
        }
        /* @var $helper WSC_MageJam_Helper_Product_Bundle */
        $helper = Mage::helper('magejam/product_bundle');
        $result['bundle_attributes'] = $helper->getBundleAttributes($product);
        $result['bundle_items'] = $helper->getBundleItems($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addGroupedItems(Mage_Catalog_Model_Product $product, &$result)
    {
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            return;
        }
        /* @var $helper WSC_MageJam_Helper_Product_Grouped */
        $helper = Mage::helper('magejam/product_grouped');
        $result['grouped_items'] = $helper->getGroupedItems($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addDownloadableInfo(Mage_Catalog_Model_Product $product, &$result)
    {
        if ($product->getTypeId() !== Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            return;
        }
        /* @var $helper WSC_MageJam_Helper_Product_Downloadable */
        $helper = Mage::helper('magejam/product_downloadable');
        $result['downloadable_info'] = $helper->getDownloadableLinks($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addConfigurableAttributes(Mage_Catalog_Model_Product $product, &$result)
    {
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return;
        }

        /* @var $helper WSC_MageJam_Helper_Product_Configurable */
        $helper = Mage::helper('magejam/product_configurable');
        $result['configurable_attributes'] = $helper->getConfigurableAttributes($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addCustomOptions(Mage_Catalog_Model_Product $product, &$result)
    {
        /* @var $helper WSC_MageJam_Helper_Product_Configurable */
        $helper = Mage::helper('magejam/product_options');
        $result['options'] = $helper->getOptionList($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $result
     */
    protected function _addMediaInfo(Mage_Catalog_Model_Product $product, &$result)
    {
        /* @var $helper WSC_MageJam_Helper_Product_Media */
        $helper = Mage::helper('magejam/product_media');
        $result['media_info'] = $helper->getMediaInfo($product);
    }


    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return bool
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
        && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     * Used for disabling flat settings
     */
    public function disableFlat($storeId = null)
    {
        Mage::app()->getStore($storeId)->setConfig(Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT, 0);
    }
}