<?php

class WSC_MageJam_Model_Checkout_Agreement_Api extends Mage_Api_Model_Resource_Abstract
{
    public function getAgreements($storeId = null)
    {
        /* @var $helper WSC_MageJam_Helper_Data */
        $helper = Mage::helper('magejam');
        $storeId = $helper->getStoreId($storeId);

        /* @var $collection Mage_Checkout_Model_Resource_Agreement_Collection */
        $collection = Mage::getResourceModel('checkout/agreement_collection');
        $collection->addStoreFilter($storeId);
        $collection->addFieldToFilter('is_active', 1);

        $response = array();
        foreach($collection as $agreement) {
            $response[] = $agreement->getData();
        }
        return $response;
    }
}