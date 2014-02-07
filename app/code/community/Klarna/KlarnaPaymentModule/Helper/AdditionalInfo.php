<?php
/**
 * File containing Klarna_KlarnaPaymentModule_Helper_AdditionalInfo class
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
 * Helper for Adititonal info
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_AdditionalInfo
    extends Mage_Core_Helper_Abstract
{

    private $_filter = array(
        "birth_day",
        "birth_month",
        "birth_year",
        "gender",
        "paymentPlan",
        "pno",
        "address_key",
        "reference",
        "invoice_type"
    );

    /**
     * Set Magento additional info.
     *
     * Based on input given from Klarna_KlarnaPaymentModule_Helper_Sanitize
     * that is expected to be ISO-8859-1
     *
     * @param Mage_Payment_Model_Info $info   payment info instance
     * @param array                   $values values to set
     *
     * @return void
     */
    public function set($info, $values)
    {
        foreach ($this->_filter as $key) {
            $info->unsAdditionalInformation($key);
        }

        foreach ($values as $key => $value) {
            if (!in_array($key, $this->_filter)) {
                continue;
            }
            if (strlen($value) == 0) {
                continue;
            }
            if ($key === 'reference') {
                $value = htmlentities($value, ENT_COMPAT, 'ISO-8859-1');
            }
            $info->setAdditionalInformation($key, $value);
        }
    }

    /**
     * Get a usable email address
     *
     * @param Mage_Customer_Model_Address_Abstract $shipping address
     * @param Mage_Customer_Model_Address_Abstract $billing  address
     * @param Mage_Checkout_Model_Session          $session  session object
     *
     * @return string
     */
    public function getEmail($shipping, $billing, $session)
    {
        //Get the email address from the address object if its set
        $addressEmail = $shipping->getEmail();
        if (strlen($addressEmail) > 0) {
            return $addressEmail;
        }

        //Otherwise we have to pick up the customers email from the session
        $sessionEmail = $session->getEmail();
        if (strlen($sessionEmail) > 0) {
            return $sessionEmail;
        }

        //For guests and new customers there wont be any email on the
        //customer object in the session or their shipping address, so we
        //have to fall back and get the email from their billing address.
        return $billing->getEmail();
    }
}
