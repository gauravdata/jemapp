<?php
/**
 * File used to handle translations
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
 * Helper class to handle translations
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Lang extends Mage_Core_Helper_Abstract
{


    /**
     * Create a locale for a specified country. This will use the current store
     * view to look up the language and currency
     *
     * @param int|string $country The country to use
     *
     * @return KiTT_Locale
     */
    public function createLocale($country)
    {
        return KiTT::locale(
            $country,
            $this->getLocaleCode(Mage::app()->getLocale()->getLocaleCode()),
            Mage::app()->getStore()-> getCurrentCurrencyCode()
        );
    }

    /**
     * Parse a locale code into a language code we can use.
     *
     * @param string $localeCode The Magento locale code to parse
     *
     * @return string
     */
    public function getLocaleCode($localeCode)
    {
        $result = preg_match("/([a-z]+)_[A-Z]+/", $localeCode, $collection);
        if ($result !== 0) {
            return $collection[1];
        }
        return null;
    }

    /**
     * Create a locale for a specified order. This will use the store view for the
     * specified order to look up the language and currency
     *
     * @param object $order A Magento order
     *
     * @return KiTT_Locale
     */
    public function createOrderLocale($order)
    {
        $localeCode = Mage::getStoreConfig(
            Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE,
            $order->getStoreId()
        );
        $languageCode = $this->getLocaleCode($localeCode);

        return KiTT::locale(
            $order->getShippingAddress()->getCountry(),
            $languageCode,
            $order->getOrderCurrencyCode()
        );
    }

}
