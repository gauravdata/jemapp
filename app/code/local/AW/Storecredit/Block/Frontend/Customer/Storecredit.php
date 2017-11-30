<?php
class AW_Storecredit_Block_Frontend_Customer_Storecredit extends Mage_Core_Block_Template
{
    public function getCmsUrl()
    {
        $store = Mage::app()->getStore();
        $cmsIdentifier = Mage::helper('aw_storecredit/config')->getCmsPageInCustomerArea($store);
        $cmsPageUrl = '';
        if ($cmsIdentifier && $cmsIdentifier !== 0) {
            $cmsPageUrl = $this->getUrl(null, array('_direct' => $cmsIdentifier, '_secure' => Mage::app()->getStore(true)->isCurrentlySecure()));
        }
        return $cmsPageUrl;
    }

    public function isCmsBlockCanShow()
    {
        $store = Mage::app()->getStore();
        $cmsIdentifier = Mage::helper('aw_storecredit/config')->getCmsPageInCustomerArea($store);
        $result = false;
        if ($cmsIdentifier && $cmsIdentifier !== 0) {
            $result = true;
        }
        return $result;
    }
}