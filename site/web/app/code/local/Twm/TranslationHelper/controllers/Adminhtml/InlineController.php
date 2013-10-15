<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Adminhtml_InlineController extends Mage_Adminhtml_Controller_Action {
    
    public function enableAction() {
        Mage::getModel('core/config')->saveConfig('dev/restrict/allow_ips', Mage::helper('core/http')->getRemoteAddr());
        Mage::getModel('core/config')->saveConfig('dev/translate_inline/active', true);
        Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Config::CACHE_TAG);
        
        $model = Mage::getModel('core/cache');
        $options = $model->canUse('');
        $options[Mage_Core_Model_Translate::CACHE_TAG] = 0;
        $model->saveOptions($options);
        
        Mage::getSingleton('adminhtml/session')
                    ->addSuccess('Inline Translations have been enabled. Translation cache has been temporarily disabled.');
            
        $this->_redirectReferer();
    }
    
    public function disableAction() {
        Mage::getModel('core/config')->saveConfig('dev/translate_inline/active', false);
        Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Config::CACHE_TAG);
        
        $model = Mage::getModel('core/cache');
        $options = $model->canUse('');
        $options[Mage_Core_Model_Translate::CACHE_TAG] = 1;
        $model->saveOptions($options);
        
        Mage::getSingleton('adminhtml/session')
                    ->addSuccess('Inline Translations have been disabled. Translation cache has been enabled again.');
        
        $this->_redirectReferer();
    }
}