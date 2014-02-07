<?php
/**
 * File used in order to format prices
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
 * Helper class to implement the KiTT_Formatter interface
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Formatter
    extends Mage_Core_Helper_Abstract
    implements KiTT_Formatter
{

    /**
     * Format the price with proper currency symbols etc
     *
     * @param mixed       $price  Raw price
     * @param KiTT_Locale $locale The locale to format the price for
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return string formatted price
     */
    public function formatPrice($price, KiTT_Locale $locale = null)
    {
        return Mage::helper('core')->formatPrice($price, false);
    }

}
