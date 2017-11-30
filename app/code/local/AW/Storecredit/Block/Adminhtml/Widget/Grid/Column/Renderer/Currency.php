<?php
class AW_Storecredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getWebsiteId();
        $code = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
        return $code;
    }

    protected function _getRate($row)
    {
        return 1;
    }
}