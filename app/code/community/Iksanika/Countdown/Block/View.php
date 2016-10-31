<?php

class Iksanika_Countdown_Block_View 
    extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('product.info.addto');
        if ($block) 
        {
            $block->setTemplate('iksanika/countdown/view.phtml');
        }
    }
}
?>