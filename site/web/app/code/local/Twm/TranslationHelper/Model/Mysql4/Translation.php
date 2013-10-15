<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Model_Mysql4_Translation extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct()
    {
        $this->_init('translationhelper/translation', 'key_id');
    }
}