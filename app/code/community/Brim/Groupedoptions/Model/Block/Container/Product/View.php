<?php
/**
 * Brim LLC Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Brim LLC Commercial Extension License
 * that is bundled with this package in the file license.pdf.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.brimllc.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@brimllc.com so we can send you a copy immediately.
 *
 * @category   Brim
 * @package    Brim_Groupedoptions
 * @copyright  Copyright (c) 2011-2012 Brim LLC
 * @license    http://ecommerce.brimllc.com/license
 */
class Brim_Groupedoptions_Model_Block_Container_Product_View extends Mage_Catalog_Block_Product_View {

    protected $_product = null;

    protected function _prepareLayout(){}

    public function setProduct(Mage_Catalog_Model_Product $product) {
        $this->_product = $product;
        return $this;
    }

    public function getProduct() {
        return $this->_product;
    }
}