<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Model_Translation extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->_init('translationhelper/translation');
    }
    
    public function deleteImportedAndMissing() {
        $collection = $this->getCollection()
                ->addFieldToFilter(array('is_imported', 'is_missing'), array(
                    array(
                        'field' => 'is_imported',
                        'eq' => true
                    ),
                    array(
                        'field' => 'is_missing',
                        'eq' => true
                    ),
                ))
                ->addFieldToFilter('is_hidden', false)
                ->load();
        $collection->delete();
    }
}