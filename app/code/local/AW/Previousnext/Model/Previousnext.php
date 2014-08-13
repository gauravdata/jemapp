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


class AW_Previousnext_Model_Previousnext extends Mage_Core_Model_Abstract
{
    const LOOP = 'previousnext/general/loopproducts';

    private $_prev = 0;
    private $_next = 0;
    private $_upCategory;
    private $_lastRequest;

    protected function _construct()
    {
        $loop = Mage::getStoreConfig(self::LOOP);
        $productIds = $this->_getProductIds();
        if (is_array($productIds)) {
            $cnt = count($productIds);
            if ($cnt > 1) {
                $pos = array_search(Mage::app()->getRequest()->getParam('id'), $productIds);
                $this->_prev = ($pos == 0) ? ($loop ? $productIds[$cnt - 1] : 0) : $productIds[$pos - 1];
                $this->_next = ($pos == ($cnt - 1)) ? ($loop ? $productIds[0] : 0) : $productIds[$pos + 1];
            }
        }
    }

    public function getPrevID()
    {
        return (int)$this->_prev;
    }

    public function getNextID()
    {
        return (int)$this->_next;
    }

    public function getUpCategoryId()
    {
        return (int)$this->_getUpCategory()->getId();
    }

    public function getUpLevelLink()
    {
        if ($this->isSearchRequest()) {
            $s = Mage::getModel('core/url')->getUrl('catalogsearch/result');
        } else {
            $s = $this->_getUpCategory()->getUrl();
        }
        if ($this->_getUpQuery()) {
            $s .= '?' . $this->_getUpQuery();
        }
        return $s;
    }

    public function isSearchRequest()
    {
        return $this->_getLastRequest()->getControllerModule() == 'Mage_CatalogSearch';
    }

    private function _getUpQuery()
    {
        return parse_url($this->_getLastRequest()->getRequestUri(), PHP_URL_QUERY);
    }

    private function _getUpCategory()
    {
        if ($this->_upCategory === null) {
            $category = Mage::getModel('catalog/category');
            $categoryId = Mage::getSingleton('core/session')->getAwPrevnextCat();
            if ($categoryId === null) {
                $currentCategory = Mage::registry('current_category');
                if ($currentCategory !== null) {
                    $categoryId = $currentCategory->getData('entity_id');
                }
            }
            if ($categoryId !== null) {
                $category->load($categoryId);
            }
            $this->_upCategory = $category;
        }
        return $this->_upCategory;
    }

    private function _getLastRequest()
    {
        if ($this->_lastRequest === null) {
            $this->_lastRequest = Mage::getSingleton('core/session')->getAwPrevnextReq();
            if ($this->_lastRequest === null) {
                $this->_lastRequest = clone Mage::app()->getRequest();
            }
        }
        return $this->_lastRequest;
    }

    private function _getProductIds()
    {
        return Mage::getSingleton('core/session')->getAwPrevnextPids();
    }

}
