<?php
class Biztech_Translator_Helper_Languages extends Mage_Core_Helper_Abstract {
    public function getLanguages() {    
        $allLanguages = Mage::app()->getLocale()->getOptionLocales();
        foreach ($allLanguages as  $language) {
            // $lang = explode('_',$key);
            $keylang= explode('_',$language['value']);
            $labellang = explode('(',$language['label']);
            $languages[$keylang[0]]= $labellang[0];
            //$languages[$language['value']]= $language['label'];
        }
        return $languages;
    }

    public function getLanguage($storeId) {
        $language = Mage::helper('translator')->getLanguage($storeId);
        return $language;
    }

    public function getFromLanguage() {
        $storeId = Mage::app()->getStore()->getId();
        $fromLanguage = Mage::helper('translator')->getFromLanguage($storeId);
        return $fromLanguage;
    }

    public function getFromLangFullName() {
        $storeId = Mage::app()->getStore()->getId();
        $language = $this->getFromLanguage();
        $allLanguages = Mage::helper('translator/languages')->getLanguages($storeId);
        if ($language)
            return $allLanguages[$language];
        else
            return 'Auto detection';
    }

    public function getBiztechTranslatorCmsConfiguration() {
        $config = array();
        $url = Mage::getUrl('adminhtml/translator/translatecmspage');
        $page_id =Mage::app()->getRequest()->getParam('page_id');
        $page = Mage::getModel('cms/page')->load($page_id)->getData();
            if(!empty($page)) {
                     $pageStoreIds = $page['store_id'];
                foreach ($pageStoreIds as $key=>$value){
                    $storeId = $value; 
                }
                if(sizeof($pageStoreIds)>1) {
                    $storeId = 1;   
                }
            }           else
            {
                $storeId = 0;   
            }
        $language = $this->getLanguage($storeId);
        $allLanguages = Mage::helper('translator/languages')->getLanguages();
        $fullFromCode = $this->getFromLanguage();
        $fullFromLanguageName = $this->getFromLangFullName();
        $translatedFields = Mage::getStoreConfig('translator/translator_general/massaction_cmspagetranslate_fields', $storeId);
        $translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
        $config['url'] = $url;
        $config['languageToFullName'] = $allLanguages[$language];
        $config['fullFromCode'] = $fullFromCode;
        $config['languageToCode'] = $language;
        $config['fullFromLanguageName'] = $fullFromLanguageName;
        $config['translatedFieldsNames'] = $translatedFields;
        $config['translateBtnText'] = $translateBtnText ? $translateBtnText : 'Translate To';
        return Mage::helper('core')->jsonEncode($config);
    }

    public function getBiztechTranslatorCategoryConfiguration($storeId) {
        $config = array();
        $url = Mage::getUrl('adminhtml/translator/translatecmspage');
        $storeId =Mage::app()->getRequest()->getParam('store',0);
        $language = $this->getLanguage($storeId);
        $allLanguages = Mage::helper('translator/languages')->getLanguages();
        $fullFromCode = $this->getFromLanguage();
        $fullFromLanguageName = $this->getFromLangFullName();
        $translatedFields = Mage::getStoreConfig('translator/translator_general/massaction_categorytranslate_fields', $storeId);
        $translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
        $config['url'] = $url;
        $config['languageToFullName'] = $allLanguages[$language];
        $config['fullFromCode'] = $fullFromCode;
        $config['languageToCode'] = $language;
        $config['fullFromLanguageName'] = $fullFromLanguageName;
        $config['translatedFieldsNames'] = $translatedFields;
        $config['translateBtnText'] = $translateBtnText ? $translateBtnText : 'Translate To';
        return Mage::helper('core')->jsonEncode($config);
    }

    public function getBiztechTranslatorCmsblockConfiguration() {
        $config = array();
        $url = Mage::getUrl('adminhtml/translator/translatecmspage');
        $block_id =Mage::app()->getRequest()->getParam('block_id');
        $block = Mage::getModel('cms/block')->load($block_id)->getData();
        if(!empty($block)) {
            $blockStoreId = $block['store_id'];           
            foreach ($blockStoreId as $key=>$value){
                $storeId = $value;                 
            }
            if(sizeof($blockStoreId)>1){
                $storeId = 1;
            }
        } else{
            $storeId=0;
        }       
        $language = $this->getLanguage($storeId);      
        $allLanguages = Mage::helper('translator/languages')->getLanguages();
        $fullFromCode = $this->getFromLanguage();
        $fullFromLanguageName = $this->getFromLangFullName();
        $translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
        $config['url'] = $url;
        $config['languageToFullName'] = $allLanguages[$language];
        $config['fullFromCode'] = $fullFromCode;
        $config['languageToCode'] = $language;
        $config['fullFromLanguageName'] = $fullFromLanguageName;
        $config['translatedFieldsNames'] = "block_content,block_title";
        $config['translateBtnText'] = $translateBtnText ? $translateBtnText : 'Translate To';       
        return Mage::helper('core')->jsonEncode($config);
    }

    public function getBiztechTranslatorReviewConfiguration() {
        $config = array();
        $url = Mage::getUrl('adminhtml/translator/translatecmspage');
        $blockId = Mage::app()->getRequest()->getParam('id');                     
        $storeArray = Mage::getModel('review/review')->load($blockId)->getData('stores');                                          
        if(sizeof($storeArray)==2) {
            $storeId = $storeArray[1];
        } else if(sizeof($storeArray)>2) {
            $storeId = 0;                       
        }
        $language = $this->getLanguage($storeId);
        $allLanguages = Mage::helper('translator/languages')->getLanguages();
        $fullFromCode = $this->getFromLanguage();
        $fullFromLanguageName = $this->getFromLangFullName();
        $translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
        $config['url'] = $url;
        $config['languageToFullName'] = $allLanguages[$language];
        $config['fullFromCode'] = $fullFromCode;
        $config['languageToCode'] = $language;
        $config['fullFromLanguageName'] = $fullFromLanguageName;
        $config['translatedFieldsNames'] = "detail";
        $config['translateBtnText'] = $translateBtnText ? $translateBtnText : 'Translate To';
        return Mage::helper('core')->jsonEncode($config);
    }

}
