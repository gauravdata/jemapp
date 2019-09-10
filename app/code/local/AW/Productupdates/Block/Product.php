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
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Productupdates_Block_Product extends Mage_Catalog_Block_Product_View
{
    private $_updateText;
    private $_blockPosition;
    private $_subscribeLinkPlaced = false;

    protected function _prepareLayout()
    {
        $this->_updateText = Mage::getBlockSingleton('productupdates/subscribelink')->getContent();
        $this->_blockPosition = Mage::getStoreConfig('productupdates/configuration/blockpositions');
        return parent::_prepareLayout();
    }

    public function getReviewsSummaryHtml(
        Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false
    )
    {
        if (
            $this->_blockPosition == AW_Productupdates_Model_Source_Declaration::UNDER_TITLE_POSITION
            && !$this->_subscribeLinkPlaced
        ) {
            $this->_subscribeLinkPlaced = true;
            return $this->_updateText . parent::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
        }
        return parent::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    public function getTierPriceHtml($product = null, $parent = null)
    {
        if (
            $this->_blockPosition == AW_Productupdates_Model_Source_Declaration::AFTER_PRICE_POSITION
        ) {
            $this->_subscribeLinkPlaced = true;
            return parent::getTierPriceHtml($product, $parent) . $this->_updateText;
        }
        return parent::getTierPriceHtml($product, $parent);
    }

    public function getChildHtml($name='', $useCache=true, $sorted=false)
    {
        if (!$this->hasOptions()) {
            $newUpdateText = "</div><div>" . $this->_updateText;
        } else {
            $newUpdateText = $this->_updateText;
        }

        if (
            $name == 'addto'
            && $this->_blockPosition == AW_Productupdates_Model_Source_Declaration::AFTER_CART_POSITION
        ) {
            $this->_subscribeLinkPlaced = true;
            return parent::getChildHtml($name, $useCache, $sorted) . $newUpdateText;
        }
        if (
            $name == 'tierprices'
            && $this->_blockPosition == AW_Productupdates_Model_Source_Declaration::AFTER_PRICE_POSITION
        ) {
            $this->_subscribeLinkPlaced = true;
            return parent::getChildHtml($name, $useCache, $sorted) . $this->_updateText;
        }
        return parent::getChildHtml($name, $useCache, $sorted);
    }

    protected function _getChildHtml($name, $useCache = true)
    {
        $newUpdateText = "<ul class='add-to-links'><li>" . $this->_updateText . "</li></ul>";

        if (
            $name == 'product.info.addto'
            && $this->_blockPosition == AW_Productupdates_Model_Source_Declaration::AFTER_CART_POSITION
            && !$this->_subscribeLinkPlaced
        ) {
            return parent::_getChildHtml($name, $useCache) . $newUpdateText;
        }
        return parent::_getChildHtml($name, $useCache);
    }
}
