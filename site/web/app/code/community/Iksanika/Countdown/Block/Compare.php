<?php
class Iksanika_Countdown_Block_Compare 
    extends Mage_Catalog_Block_Product_Compare_List
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('catalog.compare.list');
        if ($block) 
        {
            $block->setTemplate('iksanika/countdown/compare.phtml');
        }
    }
}
?>