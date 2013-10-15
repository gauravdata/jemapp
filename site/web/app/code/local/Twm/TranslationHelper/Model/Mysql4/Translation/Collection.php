<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Model_Mysql4_Translation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('translationhelper/translation');
    }
    
    public function delete() {
        /* @var $write Varien_Db_Adapter_Interface */
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $chunks = array_chunk($this->getAllIds(), 100);
        foreach ($chunks as $chunk) {
            $write->delete($this->getMainTable(), 'key_id IN (\'' . join('\',\'', $chunk) . '\')');
        }
    }

}