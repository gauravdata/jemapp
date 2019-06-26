<?php
class Biztech_Translator_Model_Config_Source_Language extends Varien_Data_Collection {
    public function toOptionArray() {
        $options = array();
        $languages = Mage::helper('translator/languages')->getLanguages();
        $options[] = array('label' => Mage::helper('translator')->__('Current locale'), 'value' => 'locale');
        foreach ($languages as $key => $language) {
            $options[] = array(
                'label' => strtoupper($key) . ': ' . $language,
                'value' => $key
            );
        }
        return ($options);
    }

    public function getFormattedOptionArray() {
        $locales = array();
        $options = array();    
        foreach (Mage::app()->getStores() as $store) {
            $locale = Mage::app()->getStore($store->getId())->getConfig('general/locale/code');
            array_push($locales, $locale);
        }        
        foreach (Mage::app()->getLocale()->getOptionLocales() as $key => $localeInfo) {
            if (in_array($localeInfo['value'], $locales)) {
                $options[$localeInfo['value']] = $localeInfo['label'];
            }
        }
        return $options;
    }
}
