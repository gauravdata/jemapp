<?php
/**
 * File containing the Checkout helper
 *
 * PHP Version 5.2
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Helper class for checkout.
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Checkout extends Mage_Core_Helper_Abstract
{

    const STATIC_KITT = "//cdn.klarna.com/public/kitt/";

    /**
     * @var Klarna_KlarnaPaymentModule_Helper_Checkout
     */
    private static $_controller;

    /**
     * Initialize the helper
     *
     * @param KiTT_Locale $locale The locale to use
     * @param float       $sum    The sum of the cart
     *
     * @return Klarna_KlarnaPaymentModule_Helper_Checkout
     */
    public function init(KiTT_Locale $locale, $sum)
    {
        $this->locale = $locale;
        $this->sum = $sum;

        return $this;
    }

    /**
     * Creates the checkout controller
     *
     * @return KiTT_Module_Checkout_Controller
     */
    private function _createController()
    {
        KiTT::setFormatter(Mage::helper('klarnaPaymentModule/formatter'));
        return KiTT::checkoutController($this->locale, $this->sum);
    }

    /**
     * Gets the checkout controller
     *
     * @return KiTT_Module_Checkout_Controller
     */
    public function getController()
    {
        if (self::$_controller === null) {
            self::$_controller = $this->_createController();
        }
        return self::$_controller;
    }

    /**
     * Convert the keys on an array to match the values needed by mustache
     *
     * @param array $values Associative array holding klarna information
     *
     * @return array
     */
    public function parseErrorData($values)
    {
        $arr = array(
            'pno' => 'string',
            'gender' => 'int',
            'birth_day' => 'string',
            'birth_month' => 'string',
            'birth_year' => 'string',
            'paymentPlan' => 'int',
            'invoice_type' => 'string',
            'reference' => 'string'
        );

        $result = array();

        foreach ($arr as $key => $type) {
            if (array_key_exists($key, $values)) {
                if ($type === 'int') {
                    $result[$key] = intval($values[$key]);
                } else {
                    $result[$key] = utf8_encode($values[$key]);
                }
            }
        }

        return $result;
    }
}
