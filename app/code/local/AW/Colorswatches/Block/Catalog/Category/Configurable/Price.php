<?php

class AW_Colorswatches_Block_Catalog_Category_Configurable_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $priceHtml = parent::_toHtml();
        return $priceHtml . $this->_getSwatchHtml();
    }

    /**
     * @return string
     */
    protected function _getSwatchHtml()
    {
        if (!AW_Colorswatches_Helper_Config::isEnabled() || !AW_Colorswatches_Helper_Config::isEnabledOnCategory()) {
            return '';
        }
        $block = $this->getLayout()->createBlock('core/template');
        $block->setProduct($this->getProduct());
        $block->setTemplate('aw_colorswatches/catalog/category/configurable.phtml');
        return $block->toHtml();
    }
}