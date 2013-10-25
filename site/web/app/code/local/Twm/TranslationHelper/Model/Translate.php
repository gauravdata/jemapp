<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Model_Translate extends Mage_Core_Model_Translate {
    
    protected $_missing = array();
    protected $_correctDataScope = array();
    protected $_themeOnly = false;
    
    public function translate($args)
    {
        $result = parent::translate($args);

        $text = array_shift($args);
	if (!is_object($text)) return;
        $code = $text->getCode();
        $text = $text->getText();

        if (trim($text) != "" && ($text === $result || $this->_getTranslatedString($text, $code) == $text)) {
            // Ignore it when the current locale is en_US, it is most likely not necessary to translate.
            // OR when the website id is 0, so ignore backend translations as well.
            if ($this->getLocale() !== 'en_US' && Mage::app()->getWebsite()->getId() > 0 && !in_array($code, $this->_missing)) {
                $model = Mage::getModel('translationhelper/translation');
                $translation = $model->getCollection()
                        ->addFieldToFilter('store_id', array(
                            'in' => array(0, Mage::app()->getStore()->getId())
                        ))
                        ->addFieldToFilter('locale', $this->getLocale());
                
                // Use where on the select object to prevent Magento from doing a quoteInto on question marks that might appear in strings.
                $select = $translation->getSelect();
                $select->where('string = ?', $code);
                
                $translation = $translation->getFirstItem();
                
                if (!$translation->getId()) {
                    $model->setString($code)
                            ->setTranslate($text)
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->setLocale($this->getLocale())
                            ->setIsMissing(true);
                    $model->save();
                } elseif ($result != $translation->getTranslate()) {
                    $result = @vsprintf($translation->getTranslate(), $args);
                    if ($result === false) {
                        $result = $translation->getTranslate();
                    }
                }
                $this->_missing[] = $code;
            }
        }
        
        return $result;
    }

    public function init($area, $forceReload = false, $themeOnly = false) {
        $this->_themeOnly = $themeOnly;
        return parent::init($area, $forceReload);
    }
    
    public function getDataScope() {
        return $this->_dataScope;
    }
    
    public function getCorrectedDataScope() {
        return $this->_correctedDataScope;
    }
    
    protected function _addData($data, $scope, $forceReload=false) {
        foreach ($data as $key => $value) {
            if ($key === $value) continue;
            $this->_correctedDataScope[$key][] = $scope;
        }
        parent::_addData($data, $scope, $forceReload);
    }

    protected function _loadModuleTranslation($moduleName, $files, $forceReload=false)
    {
        if (!$this->_themeOnly && ($forceReload || Mage::app()->getWebsite()->getId() == 0)) {
            return parent::_loadModuleTranslation($moduleName, $files, $forceReload);
        }
        return $this;
    }
    
    protected function _loadThemeTranslation($forceReload = false)
    {
        if ($this->_themeOnly) {
            $storeId = Mage::registry('translationhelper_store');
            $package = Mage::getStoreConfig('design/package/name');
            $theme = Mage::getStoreConfig('design/theme/locale', $storeId) != '' ? Mage::getStoreConfig('design/theme/locale', $storeId) : Mage::getStoreConfig('design/theme/default', $storeId);
            $file = Mage::getDesign()->getLocaleFileName('translate.csv', array(
                '_package' => $package,
                '_theme' => $theme
            ));
            $this->_addData($this->_getFileData($file), false, $forceReload);
        } elseif ($forceReload || Mage::app()->getWebsite()->getId() == 0) {
            return parent::_loadThemeTranslation($forceReload);
        }
        return $this;
    }

    protected function _loadDbTranslation($forceReload = false)
    {
        if (!$this->_themeOnly) {
            return parent::_loadDbTranslation($forceReload);
        }
        return $this;
    }
}
