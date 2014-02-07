<?php
/**
 * File containing Klarna_KlarnaPaymentModule_Helper_Sanitize class
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
 * Helper for Sanitizing (unbaptizing) input.
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Sanitize
    extends Mage_Core_Helper_Abstract
{

    /**
     * Collect the post values that are relevant to the payment method
     *
     * @param array  $data    The post values to sanitize
     * @param string $country Country code
     * @param string $method  The method index to search for
     *
     * @return array
     */
    public function get($data, $country, $method)
    {
        $values = array();
        foreach ($data as $key => $value) {
            if (strpos($key, $method) !== false) {
                // Only remove first occurance.
                $key = substr_replace($key, '', 0, strlen($method) + 1);
                $values[$key] = KiTT_String::encode(trim($value));
                continue;
            }
            if ($country == "SE") {
                //Pick up the specific OneStepCheckout keys and set them to the
                //correct values
                if ($key == "klarna_ssn") {
                    $values["pno"] = KiTT_String::encode(trim($value));
                } elseif ($key == "klarna_address_key") {
                    $values["address_key"] = KiTT_String::encode(trim($value));
                }
            }
        }

        return $values;
    }
}
