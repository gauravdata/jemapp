<?php

class Biztech_Translator_Block_Adminhtml_Translator_Cron_Renderer_Storename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getdata('store_id') == "" || $row->getdata('store_id') == "0") {
            return Mage::helper('translator')->__('Default');
        } else {
            return Mage::app()->getStore()->load($row->getStoreId())->getName();
        }
    }
}