<?php

class MT_Email_Helper_Adminhtml extends Mage_Core_Helper_Abstract
{

    public function getActiveStoreIds()
    {
        $website = Mage::app()->getRequest()->getParam('website');
        $store = Mage::app()->getRequest()->getParam('store');
        $storeIds = array();

        if ($store != '') {
            $storeId = Mage::app()->getStore($store)->getId();
            if (is_numeric($storeId))
                $storeIds [] = $storeId;

        } elseif ($website != '') {
            $website =  Mage::app()->getWebsite($website);
            if (is_numeric($website->getId())) {
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    foreach ($stores as $store) {
                        $storeIds [] = $store->getId();
                    }
                }
            }

        } else {
            $storeIds [] = 0;
        }

        return $storeIds;
    }
}