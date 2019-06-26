<?php
class Biztech_Translator_Model_System_Config_Locales {
    public function toOptionArray() {
        $locales = array();
        $options = array();
        $options['all'] = 'All';        
        foreach (Mage::app()->getStores() as $store) {
            
            $locale = Mage::app()->getStore($store->getId())->getConfig('general/locale/code');
            array_push($locales, $locale);
        }
        foreach (Mage::app()->getLocale()->getOptionLocales() as $key => $localeInfo) {
            if (in_array($localeInfo['value'], $locales)) {
                $options[$localeInfo['value']] =  $localeInfo['label'];
            }
        }
        return $options;
    }
}
