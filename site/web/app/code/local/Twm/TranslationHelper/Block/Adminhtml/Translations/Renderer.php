<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Block_Adminhtml_Translations_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row)
    {
        $column = $this->getColumn()->getIndex();
        $value = $row->getData($column);
        if ($column == 'store_id') {
            if ($value == 0) return 'Any';
            $store = Mage::app()->getStore($value);
            return $store->getName();
        } elseif ($column == 'locale') {
            $locales = Mage::helper('translationhelper')->getLocales();
            if (isset($locales[$value])) {
                return $locales[$value];
            }
        }
        
        return $value;
    }

}