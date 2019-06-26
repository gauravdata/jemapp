<?php

class Biztech_Translator_Block_Adminhtml_Translator_Cron_Renderer_Langto extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getdata('lang_to') == "") {
            return Mage::helper('translator')->__('Auto Language Detect');
        } else {
        	return $fullNameLanguage = Mage::helper('translator')->getLanguageFullNameByCode($row->getLangTo(), $row->getStoreId());
        }
    }
}