<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getLocales() {
        $storeLocales = array();
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $store->getId()));
            if (!in_array($locale->toString(), $storeLocales)) {
                $storeLocales[] = $locale->toString();
            }
        }
        $locale = Mage::getModel('core/locale');
        $locales = $locale->getOptionLocales();
        $options = array();
        foreach ($locales as $locale) {
            if (in_array($locale['value'], $storeLocales)) {
                $options[$locale['value']] = $locale['label'];
            }
        }
        return $options;
    }

    public function getStores() {
        $options = array();
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $options[$store->getId()] = $store->getName();
        }
        return $options;
    }

}