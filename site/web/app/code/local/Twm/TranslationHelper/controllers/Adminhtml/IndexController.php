<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {
        
    public function indexAction() {
        $this->loadLayout();
        $this->_setActiveMenu('cms/translationhelper');
        $this->_addBreadcrumb('CMS', 'Translation Helper');
        $this->_addContent($this->getLayout()->createBlock('translationhelper/adminhtml_grid'));
        $this->renderLayout();
    }
    
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $translationid = $this->getRequest()->getParam('id');
        $model = Mage::getModel('translationhelper/translation')->load($translationid);
        if ($model->getId() || $translationid == 0) {
            if ($model->getId()) {
                Mage::register('translation_data', $model);
            }
            $this->loadLayout();
            $this->_setActiveMenu('cms/translationhelper');
            $this->_addBreadcrumb('CMS', 'Translation Helper');
            $this->_addContent($this->getLayout()->createBlock('translationhelper/adminhtml_translations_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Translation does not exist');
            $this->_redirect('*/*/');
        }
    }
    
    public function duplicateAction() {
        $translationid = $this->getRequest()->getParam('duplicate_id');
        $model = Mage::getModel('translationhelper/translation')->load($translationid);
        if ($model->getId()) {
            $model->unsetData('key_id');
            Mage::register('translation_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('cms/translationhelper');
            $this->_addBreadcrumb('CMS', 'Translation Helper');
            $this->_addContent($this->getLayout()->createBlock('translationhelper/adminhtml_translations_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Translation does not exist');
            $this->_redirect('*/*/');
        }
    }
    
    public function saveAction() {
        $request = $this->getRequest();
        if ($request->getPost()) {
            try {
                $postData = $request->getPost();
                $model = Mage::getModel('translationhelper/translation');

                if ($request->getParam('id') <= 0) {
                    $model->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                }

                $model->addData($postData)
                        ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
                        ->setId($request->getParam('id'))
                        ->save();
                
                Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Translate::CACHE_TAG);
                
                Mage::getSingleton('adminhtml/session')
                        ->addSuccess('Successfully saved');
                Mage::getSingleton('adminhtml/session')
                        ->settestData(false);
                
                if ($request->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit/id/' . $request->getParam('id'));
                    return;
                }
                
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                        ->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')
                        ->settestData($this->getRequest()
                                ->getPost());

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                            ->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('translationhelper/translation');
                $model->setId($this->getRequest()
                                ->getParam('id'))
                        ->delete();
                Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Config::CACHE_TAG);
                Mage::getSingleton('adminhtml/session')
                        ->addSuccess('successfully deleted');
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                        ->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function massHideAction() {
        $translationIds = $this->getRequest()->getParam('key_id');
        if (!is_array($translationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translationhelper')->__('Please select tax(es).'));
        } else {
            try {
                $model = Mage::getModel('translationhelper/translation');
                foreach ($translationIds as $translationid) {
                    $model->load($translationid)
                            ->setIsHidden(true)
                            ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('translationhelper')->__(
                                'Total of %d translation(s) were hidden.', count($translationIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massUnhideAction() {
        $translationIds = $this->getRequest()->getParam('key_id');
        if (!is_array($translationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translationhelper')->__('Please select translations.'));
        } else {
            try {
                $model = Mage::getModel('translationhelper/translation');
                foreach ($translationIds as $translationid) {
                    $model->load($translationid)
                            ->setIsHidden(false)
                            ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('translationhelper')->__(
                                'Total of %d translation(s) were made visible.', count($translationIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction() {
        $translationIds = $this->getRequest()->getParam('key_id');
        if (!is_array($translationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translationhelper')->__('Please select translations.'));
        } else {
            try {
                $model = Mage::getModel('translationhelper/translation');
                foreach ($translationIds as $translationid) {
                    $model->setId($translationid)
                        ->delete();
                }
                Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Config::CACHE_TAG);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('translationhelper')->__(
                                'Total of %d translation(s) were deleted.', count($translationIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function refreshAction() {

        Mage::getModel('translationhelper/translation')->deleteImportedAndMissing();

        $model = Mage::getModel('core/cache');
        $options = $model->canUse('');
        $options[Mage_Core_Model_Translate::CACHE_TAG] = 0;
        $model->saveOptions($options);

        $stores = Mage::app()->getStores();
        $locales = array();
        foreach ($stores as $store) {
            $locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $store->getId()));
            if (!in_array($locale->toString(), $locales)) {
                $locales[] = $locale->toString();
            }
        }

        foreach ($locales as $locale) {
            Mage::getSingleton('core/translate')->setLocale(new Zend_Locale($locale))->init('frontend', true);
            $this->reloadTranslations();
        }

        foreach ($stores as $store) {
            $locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $store->getId()));
            Mage::unregister('translationhelper_store');
            Mage::register('translationhelper_store', $store->getId());
            Mage::getSingleton('core/translate')->setLocale($locale)->init('frontend', true, true);
            $this->reloadTranslations($store->getId(), true);
        }

        Mage::app()->getCacheInstance()->cleanType(Mage_Core_Model_Config::CACHE_TAG);

        $model = Mage::getModel('core/cache');
        $options = $model->canUse('');
        $options[Mage_Core_Model_Translate::CACHE_TAG] = 1;
        $model->saveOptions($options);
        
        $this->_redirect('*/*/index');
    }

    protected function reloadTranslations($storeId = 0, $themeOnly = false)
    {
        $coreTranslate = Mage::getSingleton('core/translate');
        $dataScope = $coreTranslate->getCorrectedDataScope();
        foreach ($coreTranslate->getData() as $string => $translate) {
            $translation = Mage::getModel('translationhelper/translation')
                ->setString($string)
                ->setTranslate($translate)
                ->setLocale($coreTranslate->getLocale()->toString())
                ->setStoreId($storeId);

            $strings = array($string);
            if (!$themeOnly && isset($dataScope[$string])) {
                $strings = array();
                foreach ($dataScope[$string] as $scope) {
                    $str = (!empty($scope) ? $scope . Mage_Core_Model_Translate::SCOPE_SEPARATOR : '') . $string;
                    if (!in_array($str, $strings)) {
                        $strings[] = (!empty($scope) ? $scope . Mage_Core_Model_Translate::SCOPE_SEPARATOR : '') . $string;
                    }
                }
            }

            foreach ($strings as $str) {
                $translation->setString($str);
                $collection = Mage::getModel('translationhelper/translation')->getCollection()
                    ->addFieldToFilter('locale', $translation->getLocale())
                    ->addFieldToFilter(array('is_imported', 'is_missing'), array(
                        array(
                            'field' => 'is_missing',
                            'eq' => true
                        ),
                        array(
                            'field' => 'is_imported',
                            'eq' => true
                        )
                    ))
                    ->addFieldToFilter('store_id', array('in' => array(0, $storeId)));

                $models = array();
                // Use where on the select object to prevent Magento from doing a quoteInto on question marks that might appear in strings.
                if (!$themeOnly) {
                    $collection->getSelect()->where('string = ?', $translation->getString());
                    $model = $collection->getFirstItem();
                    if ($model->getId()) {
                        $models[] = $model;
                    }
                } else {
                    $select = $collection->getSelect();
                    $select->where($select->getAdapter()->quoteInto('string LIKE ?', '%' . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $translation->getString()) . ' OR ' . $select->getAdapter()->quoteInto('string = ?', $translation->getString()));
                    $models = $collection->getItems();
                }

                foreach ($models as $model) {
                    $model->delete();
                }

                $translation->setIsImported(true);
                $translation->save();
                $translation->unsetData('key_id');
            }
        }
    }
}