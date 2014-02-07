<?php

/**
 * Class to show our product page box.
 *
 * PHP Version 5.3
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Product Price extension
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Block_Product_Price
    extends Mage_Bundle_Block_Catalog_Product_Price
{

    protected $controller;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        Mage::helper('klarnaPaymentModule/api')->loadConfig();
        $data = Mage::helper("klarnaPaymentModule");
        $address = $data->guessCustomerAddress();
        $location = $data->location($address);

        if ($this->getProduct() !== null && $location !== null) {
            KiTT::setFormatter(Mage::helper('klarnaPaymentModule/formatter'));

            $this->controller = KiTT::productController(
                KiTT::locale($location), $this->_getPrice()
            );

        }
    }
    /**
     * Override toHtml and append our own Html
     *
     * @return string
     */
    // @codingStandardsIgnoreStart method name required by Magento
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        return $this->_getHtml($html);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Get our HTML and append it to magentos HTML.
     *
     * @param string $html HTML string to append more data to.
     *
     * @return string HTML to display.
     */
    private function _getHtml($html)
    {

        if (!$this->_shouldShow()) {
            return $html;
        }

        // It's a normal product, should be enough space for the part
        // payment box
        $html .= $this->getLayout()->createBlock(
            'core/template', 'klarna_price_block'
        )->setTemplate('klarna/product/price.phtml')->toHtml();

        $html .= $this->controller->createWidget()->show();

        return $html;
    }

    /**
     * Special showing conditions for magento.
     *
     * @return boolean true if the ppbox should be shown
     */
    private function _shouldShow()
    {

        if ($this->controller === null) {
            return false;
        }

        if (!$this->controller->isAvailable()) {
            return false;
        }
        $storeId = Mage::app()->getStore()->getId();

        $isActive = Mage::getStoreConfig(
            'klarna/payments/klarna_partpayment_enabled', $storeId
        );

        if (!$isActive) {
            return false;
        }

        if (!$this->getTemplate() == "catalog/product/price.phtml"
            && !$this->getTemplate() == "bundle/catalog/product/price.phtml"
        ) {
            return false;
        }

        // Load the main product displayed on the page and see if it is a
        // collection.
        $tmpProduct = Mage::getModel(
            'catalog/product'
        )->load(Mage::app()->getRequest()->getParam('id'));

        // Don't show box for collections for dutch customers
        if ($tmpProduct->getGroupedLinkCollection()->count() > 0) {
            return false;
        }

        // If we are at product page and already have created the price
        // block we do not want to do that again
        if ($this->getLayout()->getBlock('klarna_price_block')) {
            return false;
        }

        // Only display this for primary products and not related, cross or
        // up-sell products the main products will not have a suffix
        if ($this->getIdSuffix() != null) {
            return false;
        }

        return true;
    }

    /**
     * Get the price of the product
     *
     * @return float
     */
    private function _getPrice()
    {
        $_taxHelper = $this->helper('tax');
        $curr = Mage::app()->getStore()->getCurrentCurrencyCode();
        $base_currency = Mage::app()->getStore()->getBaseCurrencyCode();
        $convert = ($base_currency != $curr);

        $rate = 1;
        if ($convert) {
            $cCurr = Mage::getModel('directory/currency');
            $cCurr->load($base_currency);
            $rate = $cCurr->getRate($curr);
        }

        if ($this->getProduct()->getSpecialPrice() > 0) {
            return $_taxHelper->getPrice(
                $this->getProduct(),
                $this->getProduct()->getSpecialPrice(),
                true
            ) * $rate;
        }
        return $_taxHelper->getPrice(
            $this->getProduct(),
            $this->getProduct()->getFinalPrice(),
            true
        ) * $rate;
    }

}
