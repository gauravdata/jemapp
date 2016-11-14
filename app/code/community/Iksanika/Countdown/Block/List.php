<?php
class Iksanika_Countdown_Block_List 
    extends Mage_Catalog_Block_Product_List
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('product_list');
        if ($block) 
        {
            $block->setTemplate('iksanika/countdown/list.phtml');
        }

        $block = $this->getLayout()->getBlock('search_result_list');
        if ($block) 
        {
            $block->setTemplate('iksanika/countdown/list.phtml');
        }
    }
}
?>