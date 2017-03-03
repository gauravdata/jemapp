<?php
/**
 * Created by PhpStorm.
 * User: freek
 * Date: 23-2-17
 * Time: 14:34
 */ 
class Twm_ExtendZblocks_Model_Source_Position extends AW_Zblocks_Model_Source_Position {

    /*
     * Extend "Product only" in options array with "Product Image Box Bottom"
     *
     */
    public function toOptionArray() {
        $options = parent::toOptionArray();

        $helper = Mage::helper('zblocks');
        $options[5] = array(
            'label' => $helper->__('Product only'),
            'value' => array(
                array('value' => 'product-sidebar-right-top', 'label' => $helper->__('Product Sidebar Right Top')),
                array('value' => 'product-sidebar-right-bottom', 'label' => $helper->__('Product Sidebar Right Bottom')),
                array('value' => 'product-sidebar-left-top', 'label' => $helper->__('Product Sidebar Left Top')),
                array('value' => 'product-sidebar-left-bottom', 'label' => $helper->__('Product Sidebar Left Bottom')),
                array('value' => 'product-content-top', 'label' => $helper->__('Product Content Top')),
                array('value' => 'product-menu-top', 'label' => $helper->__('Product Menu Top')),
                array('value' => 'product-menu-bottom', 'label' => $helper->__('Product Menu Bottom')),
                array('value' => 'product-page-bottom', 'label' => $helper->__('Product Page Bottom')),
                array('value' => 'product-img-box-bottom', 'label' => $helper->__('Product Image Box Bottom')),
            ),
            '_needs_category' => true,
            '_needs_product' => true
        );

        return $options;
    }
}