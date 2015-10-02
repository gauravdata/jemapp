<?php

class Shopworks_BillinkOsc_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns if One Step Checkout is enabled
     * @return bool
     */
    public function isOscEnabled()
    {
        //It is possible that the module is uninstalled, but that the config is not cleaned. Therefore we should not
        //only check the config, but also is the module is enabled
        $isModuleEnabled = (bool)Mage::helper('core')->isModuleEnabled('Idev_OneStepCheckout');
        $isConfiguratedToBeActive = (bool)Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
        return $isModuleEnabled && $isConfiguratedToBeActive;
    }
}