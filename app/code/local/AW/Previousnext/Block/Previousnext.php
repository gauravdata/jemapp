<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Previousnext
 * @version    1.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Previousnext_Block_Previousnext extends Mage_Catalog_Block_Product_Abstract
{
    const LOOP = 'previousnext/general/loopproducts';
    const DISPLAY_CONTROLS = 'previousnext/general/displaycontrols';
    const STRING_LENGTH = 'previousnext/general/symbolsnumber';
    const STRING_ENDING = 'previousnext/general/ending';
    const DISPLAY_PRODUCT_THUMBNAILS = 'previousnext/general/display_product_thumbnails';
    const PRODUCT_THUMBNAIL_HEIGHT = 'previousnext/general/product_thumbnail_height';
    const PRODUCT_THUMBNAIL_WIDTH = 'previousnext/general/product_thumbnail_width';

    const UP_ENABLED = 'previousnext/upcontrol/upcontrol';

    const PREVIOUS_LINK_TEXT = 'previousnext/previouscontrol/linktext';
    const NEXT_LINK_TEXT = 'previousnext/nextcontrol/linktext';
    const UP_LINK_TEXT = 'previousnext/upcontrol/linktext';

    const PREVIOUS_IMAGE = 'previousnext/previouscontrol/image';
    const NEXT_IMAGE = 'previousnext/nextcontrol/image';
    const UP_IMAGE = 'previousnext/upcontrol/image';
    const DISPLAY_CATEGORY_THUMBNAILS = 'previousnext/upcontrol/display_category_thumbnails';

    const STRING_LENGTH_BY_DEFAULT = 40;
    const PRODUCT_THUMBNAIL_HEIGHT_BY_DEFAULT = 100;
    const PRODUCT_THUMBNAIL_WIDTH_BY_DEFAULT = 100;
    const FIRST_PAGE_INDEX = 1;

    protected $_previousProduct;
    protected $_nextProduct;
    protected $_upCategory;
    protected $_upLevelLink = '';
    protected $_isSearch;

    protected function _toHtml()
    {
        /* If customer want to disable standart display of PN block, he can insert
         * block initialization from phtml. from_xml variable is a flag in xml file,
         * that means that block is calling from xml
         */
        if ($this->getFromXml() == 'yes' && !$this->isDisplayControls()) {
            return parent::_toHtml();
        }

        $this->_setLinksforProduct();
        $this->setTemplate("previousnext/links.phtml");
        return parent::_toHtml();
    }

    /**
     * Set $this->_previousProduct and $this->_nextProduct variables
     */
    protected function _setLinksforProduct()
    {
        $currentProduct = Mage::registry('current_product');

        $products = $this->_getProducts($currentProduct);
        $productIds = $this->_getProductIds($products);

        $prevId = $nextId = 0;
        foreach ($productIds as $index => $value) {
            if ($value == $currentProduct->getId()) {
                $prevId = $this->_initPrevId($productIds, $index, $products);
                $nextId = $this->_initNextId($productIds, $index, $products);
            }
        }
        $currentStoreId = Mage::app()->getStore()->getId();
        /** @var $links AW_Previousnext_Model_Previousnext */
        $links = Mage::getModel('previousnext/previousnext');
        $this->_previousProduct = Mage::getModel('catalog/product')->setStoreId($currentStoreId)->load(
            $links->getPrevID()
        );
        $this->_nextProduct = Mage::getModel('catalog/product')->setStoreId($currentStoreId)->load($links->getNextID());
        $this->_upCategory = Mage::getModel('catalog/category')->setStoreId($currentStoreId)->load($links->getUpCategoryId());
        $this->_upLevelLink = $links->getUpLevelLink();
        $this->_isSearch = $links->isSearchRequest();
    }

    protected function _initPrevId($productIds, $index, $products)
    {
        if (($index - 1) >= 0) {
            $prevId = $productIds[$index - 1];
        } elseif ($this->_isNotFirstPage($products)) {

            $_prevProducts = clone $products;
            $_prevProducts->clear();
            $_prevProducts->setCurPage($_prevProducts->getCurPage() - 1);
            $_prevProducts->load();

            $productIds = $this->_getProductIds($_prevProducts);
            $prevId = $productIds[$_prevProducts->getPageSize() - 1];

            $collectionToUpdate = $this->_concatCollections($_prevProducts, $products);

            Mage::app()->saveCache(
                serialize($collectionToUpdate), $this->_getCacheIdForSession('aw_collection_to_process'), array(), null
            );

        } elseif (Mage::getStoreConfig(self::LOOP)) {
            $_prevProducts = clone $products;
            $_prevProducts->clear();
            $_prevProducts->setCurPage($_prevProducts->getLastPageNumber());
            $_prevProducts->load();

            $productIds = $this->_getProductIds($_prevProducts);
            $prevId = $productIds[count($_prevProducts) - 1];
            $collectionToUpdate = $this->_concatCollections($_prevProducts, $products);

            Mage::app()->saveCache(
                serialize($collectionToUpdate), $this->_getCacheIdForSession('aw_collection_to_process'), array(), null
            );

        } else {
            $prevId = -1;
        }
        return $prevId;
    }

    protected function _initNextId($productIds, $index, $products)
    {
        if (($index + 1) != count($productIds)) {
            $nextId = $productIds[$index + 1];
        } elseif ($this->_isNotLastPage($products)) {

            $_nextProducts = clone $products;

            $_nextProducts->clear();
            $_nextProducts->setCurPage($_nextProducts->getCurPage() + 1);
            $_nextProducts->load();

            $productIds = $this->_getProductIds($_nextProducts);

            $nextId = $productIds[0];
            $collectionToUpdate = $this->_concatCollections($products, $_nextProducts);

            Mage::app()->saveCache(
                serialize($collectionToUpdate), $this->_getCacheIdForSession('aw_collection_to_process'), array(), null
            );

        } elseif (Mage::getStoreConfig(self::LOOP)) {
            $nextId = $productIds[0];
        } else {
            $nextId = -1;
        }
        return $nextId;
    }

    protected function _isNotLastPage($products)
    {
        $result = false;
        if ($products->getCurPage() != $products->getLastPageNumber()) {
            $result = true;
        }
        return $result;
    }

    protected function _isNotFirstPage($products)
    {
        $result = false;
        if ($products->getCurPage() != self::FIRST_PAGE_INDEX) {
            $result = true;
        }
        return $result;
    }

    protected function _getProductIds($products)
    {
        $productIds = array();
        if ($products) {
            if (!$products->isLoaded()) {
                foreach ($products as $key => $item) {
                    $productIds[] = $key;
                }
            } else {
                $productIds = $products->getLoadedIds();
            }
        }
        return $productIds;
    }

    protected function _getProducts($currentProduct)
    {
        $products = unserialize(Mage::app()->loadCache($this->_getCacheIdForSession('aw_collection_to_process')));
        $ids = unserialize(Mage::app()->loadCache($this->_getCacheIdForSession('aw_array_to_process')));
        if (!empty($ids)) {
            foreach ($ids as $index => $value) {
                if (in_array($currentProduct->getId(), $value)) {
                    $products->clear();
                    $products->setCurPage($index);
                    $products->load();
                }
            }
        }
        return $products;
    }

    protected function _concatCollections($baseCollection, $collectionToAdd)
    {
        $_baseCurrPage = $baseCollection->getCurPage();
        $_collectionToAdd = $collectionToAdd->getCurPage();

        $ids[$_baseCurrPage] = $baseCollection->getLoadedIds();
        $ids[$_collectionToAdd] = $collectionToAdd->getLoadedIds();

        Mage::app()->saveCache(serialize($ids), $this->_getCacheIdForSession('aw_array_to_process'), array(), null);
        foreach ($collectionToAdd as $item) {
            if (is_null($baseCollection->getItemById($item->getId()))) {
                $baseCollection->addItem($item);
            }
        }
        return $baseCollection;
    }

    protected function _getCacheIdForSession($param)
    {
        $sessionId = Mage::getModel("core/session")->getEncryptedSessionId();
        return $param . "_" . $sessionId;
    }

    protected function getPreviousProductText()
    {
        if ($this->_previousProduct->getId() == Mage::registry('current_product')->getId()) {
            return '';
        }
        return $this->getFormatedText(Mage::getStoreConfig(self::PREVIOUS_LINK_TEXT), $this->_previousProduct);
    }

    public function getNextProductText()
    {
        if ($this->_nextProduct->getId() == Mage::registry('current_product')->getId()) {
            return '';
        }
        return $this->getFormatedText(Mage::getStoreConfig(self::NEXT_LINK_TEXT), $this->_nextProduct);
    }

    public function getUpLevelText()
    {
        return $this->getFormatedText(Mage::getStoreConfig(self::UP_LINK_TEXT));
    }

    /**
     * Format linkText string length, replace #PRODUCT and $CATEGORY variables to product and category names
     *
     * @param string $linkText
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    protected function getFormatedText($linkText, $product = null)
    {
        if ($product) {
            $productName = $product->getName();
            $origLength = strlen($productName);
            $ready = mb_substr($productName, 0, $this->getStringLength(), 'utf-8');
            $newLength = strlen($ready);

            if ($newLength < $origLength) {
                $ready .= Mage::getStoreConfig(self::STRING_ENDING);
            }

            $readyLink = str_replace('#PRODUCT#', $ready, $linkText);
        } else {
            if ($this->_isSearch) {
                $categoryName = Mage::helper('previousnext')->__('Search Results');
            } else {
                $categoryName = $this->_upCategory->getName();
            }

            $origLength = strlen($categoryName);
            $ready = mb_substr($categoryName, 0, $this->getStringLength(), 'utf-8');
            $newLength = strlen($ready);

            if ($newLength < $origLength) {
                $ready .= Mage::getStoreConfig(self::STRING_ENDING);
            }
            $readyLink = str_replace('#CATEGORY#', $ready, $linkText);
        }
        return $readyLink;
    }

    public function getPreviousProductLabel()
    {
        return htmlspecialchars($this->_previousProduct->getName());
    }

    public function getNextProductLabel()
    {
        return htmlspecialchars($this->_nextProduct->getName());
    }

    public function getUpCategoryLabel()
    {
        if ($this->_isSearch) {
            return Mage::helper('previousnext')->__('Search Results');
        } elseif ($this->_upCategory) {
            return htmlspecialchars($this->_upCategory->getName());
        }
        return '';
    }

    public function getPreviousProductImage()
    {
        return $this->getImageFromConfig(Mage::getStoreConfig(self::PREVIOUS_IMAGE));
    }

    public function getNextProductImage()
    {
        return $this->getImageFromConfig(Mage::getStoreConfig(self::NEXT_IMAGE));
    }

    public function getUpLevelImage()
    {
        return $this->getImageFromConfig(Mage::getStoreConfig(self::UP_IMAGE));
    }

    protected function getImageFromConfig($path)
    {
        if ($path) {
            return Mage::getBaseUrl('media') . 'catalog/product/awpn/' . $path;
        }
        return '';
    }

    protected function getStringLength()
    {
        $str = (int)Mage::getStoreConfig(self::STRING_LENGTH);
        if ($str == 0) {
            $str = self::STRING_LENGTH_BY_DEFAULT;
        }
        return $str;
    }

    public function getPreviousProductLink()
    {
        if ($this->_previousProduct->getId()) {
            return $this->getProductLinkUrl($this->_previousProduct);
        }
        return '';
    }

    public function getNextProductLink()
    {
        if ($this->_nextProduct->getId()) {
            return $this->getProductLinkUrl($this->_nextProduct);
        }
        return '';
    }

    public function getUpLevelLink()
    {
        if (Mage::getStoreConfig(self::UP_ENABLED)) {
            return $this->_upLevelLink;
        }
        return '';
    }

    /**
     * Get link for product
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    protected function getProductLinkUrl($product)
    {
        $additional = array();
        if (!Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY)) {
            $additional['_ignore_category'] = true;
        }
        $url = $product->getUrlModel()->getUrl($product, $additional);
        return $url;
    }

    public function getPreviousProductThumbUrl()
    {
        if ($this->_previousProduct->getId()) {
            return $this->getProductThumbUrl($this->_previousProduct);
        }
        return '';
    }

    public function getNextProductThumbUrl()
    {
        if ($this->_nextProduct->getId()) {
            return $this->getProductThumbUrl($this->_nextProduct);
        }
        return '';
    }

    /**
     * Get thumb image for product
     */
    protected function getProductThumbUrl($product)
    {
        if ($this->isDisplayProductThumbnails()) {
            $thumb = $this->helper('catalog/image')
                ->init(
                    Mage::getModel('catalog/product')->load($product->getId()),
                    'thumbnail'
                )
                ->resize(
                    $this->getProductThumbnailWidth(),
                    $this->getProductThumbnailHeight()
                )
            ;
            return $thumb;
        }
        return '';
    }

    public function getUpCategoryThumbUrl()
    {
        if ($this->_isSearch) {
            return '';
        }
        if ($this->isDisplayCategoryThumbnails()) {
            if ($this->_upCategory->getThumbnail()) {
                return Mage::getBaseUrl('media') . 'catalog/category/' . $this->_upCategory->getThumbnail();
            } elseif ($this->_upCategory->getImage()) {
                return Mage::getBaseUrl('media') . 'catalog/category/' . $this->_upCategory->getImage();
            }
        }
        return '';
    }

    public function isDisplayProductThumbnails()
    {
        return Mage::getStoreConfig(self::DISPLAY_PRODUCT_THUMBNAILS);
    }

    public function isDisplayCategoryThumbnails()
    {
        return Mage::getStoreConfig(self::DISPLAY_CATEGORY_THUMBNAILS);
    }

    public function getProductThumbnailHeight()
    {
        $value = (int)Mage::getStoreConfig(self::PRODUCT_THUMBNAIL_HEIGHT);
        if ($value == 0) {
            $value = self::PRODUCT_THUMBNAIL_HEIGHT_BY_DEFAULT;
        }
        return $value;
    }

    public function getProductThumbnailWidth()
    {
        $value = (int)Mage::getStoreConfig(self::PRODUCT_THUMBNAIL_WIDTH);
        if ($value == 0) {
            $value = self::PRODUCT_THUMBNAIL_WIDTH_BY_DEFAULT;
        }
        return $value;
    }

    public function isDisplayControls() {
        return Mage::getStoreConfig(self::DISPLAY_CONTROLS);
    }

}
