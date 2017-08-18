<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


class Amasty_Promo_Block_Items_Item extends Mage_Catalog_Block_Product_Abstract
{
    protected $_template = 'amasty/ampromo/items/item.phtml';

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        $params = Mage::helper('ampromo')->getUrlParams();

        return $this->getUrl('ampromo/cart/update', $params);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param bool|false $displayMinimalPrice
     * @param string $idSuffix
     * @return mixed|string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        if ($product->getAmpromoShowOrigPrice()) {

            if ($product->getTypeId() == 'giftcard') {
                $_amount = Mage::helper("ampromo")->getGiftcardAmounts($product);
                $_amount = array_shift($_amount);
                $product->setPrice($_amount);
            }

            $html = parent::getPriceHtml($product, $displayMinimalPrice, $idSuffix);

            if ($product->getSpecialPrice() == 0) {
                $html = str_replace('regular-price', 'old-price', $html);
            }
            return $html;
        }

        return '';
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();

        /** @var Mage_Catalog_Helper_Product_Type_Composite $compositeProductHelper */
        $compositeProductHelper = $this->helper('catalog/product_type_composite');
        $config = array_merge(
            $compositeProductHelper->prepareJsonGeneralConfig(),
            $compositeProductHelper->prepareJsonProductConfig($product)
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object' => $responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getTypeOptionsHtml()
    {
        $product = $this->getProduct();

        if (Mage::registry('current_product')) {
            Mage::unregister('current_product');
        }

        Mage::register('current_product', $product);

        switch ($product->getTypeId()) {
            case 'downloadable':
                $_blockOpt = 'downloadable/catalog_product_links';
                $_templateOpt = 'amasty/ampromo/items/downloadable.phtml';
                break;
            case 'configurable':
                $_blockOpt = 'catalog/product_view_type_configurable';
                $_templateOpt = 'amasty/ampromo/items/configurable.phtml';
                break;
            case 'bundle':
                $_blockOpt = 'ampromo/items_bundle';
                $_templateOpt = 'bundle/catalog/product/view/type/bundle/options.phtml';
                break;
            case 'amgiftcard':
                $_blockOpt = 'amgiftcard/catalog_product_view_type_giftCard';
                $_templateOpt = 'amasty/amgiftcard/catalog/product/view/type/giftcard.phtml';
                break;
            case 'virtual':
                $_blockOpt = 'catalog/product_view_type_virtual';
                break;
            case 'giftcard':
                $_blockOpt = 'enterprise_giftcard/catalog_product_view_type_giftcard';
                $_templateOpt = 'amasty/ampromo/items/giftcard.phtml';
                break;
            case 'amstcred':
                $_blockOpt = 'amstcred/catalog_product_view_type_storeCredit';
                $_templateOpt = 'amasty/amstcred/catalog/product/view/type/amstcred.phtml';
                break;
        }

        if (!empty($_blockOpt) && !empty($_templateOpt)) {
            $block = $this->getLayout()
                ->createBlock(
                    $_blockOpt,
                    'ampromo_item_' . $product->getId(),
                    array('product' => $product)
                )
                ->setProduct($product)
                ->setTemplate($_templateOpt);

            switch ($product->getTypeId()) {
                case 'giftcard':
                    $child = $this->getLayout()->createBlock(
                            'enterprise_giftcard/catalog_product_view_type_giftcard_form',
                            'product.info.giftcard.form'
                        )
                        ->setTemplate('giftcard/catalog/product/view/type/giftcard/form.phtml');

                    $block->setChild('product.info.giftcard.form', $child);
                    break;
            }

            return $block->toHtml();
        }
    }

    public function customOptionsHtml()
    {
        return $this->getLayout()
            ->createBlock('ampromo/items_options', '', array('product' => $this->getProduct()))
            ->toHtml();
    }

    public function getImageUrl(Mage_Catalog_Model_Product $product, $width, $height)
    {
        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = Mage::helper('catalog/image');

        $image = $helper->init($product, 'small_image')->resize($width, $height);

        return (string)$image;
    }
}
