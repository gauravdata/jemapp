<?php
/**
 * File used for handling the advanced Klarna calls
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
 * Klarna_KlarnaPaymentModule_Helper_Gateway_Advanced
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Gateway_Advanced
    extends Klarna_KlarnaPaymentModule_Helper_Gateway_Abstract
{

    /**
     * Get the flags used for the activation call
     *
     * @return KlarnaFlags
     */
    private function _getFlags()
    {
        $flag = KlarnaFlags::RSRV_PRESERVE_RESERVATION;

        // Add alternate credit time if we have a company
        if (Mage::getStoreConfig("klarna/advanced/altcredtime") == "1"
            && (strlen($this->order->getShippingAddress()->getCompany()) > 0)
        ) {
            return $flag |= 128;
        }

        return $flag;
    }

    /**
     * Perform the transaction call
     *
     * @return void
     */
    public function transaction()
    {
        $this->updateGoodsList();
        $this->setAddresses();

        $result = $this->klarna->reserveAmount(
            $this->getPNO(),
            $this->getGender(),
            $this->order->getTotalDue(),
            KlarnaFlags::NO_FLAG,
            $this->getPaymentPlan()
        );

        return array(
            "klarna_transaction_id" => $result[0],
            "klarna_status" => $result[1]
        );
    }

    /**
     * Perform the activation call
     *
     * @param array $items The items to add
     *
     * @return void
     */
    public function activate($items)
    {
        $this->updateGoodsList($items);
        $this->setAddresses();

        $result = $this->klarna->activateReservation(
            $this->getPNO(),
            $this->getTransactionId(),
            $this->getGender(),
            "",
            $this->_getFlags(),
            $this->getPaymentPlan()
        );

        $host = Mage::getStoreConfig("klarna/general/host");
        $domain = ($host === 'BETA') ? 'beta-test': 'online';
        $link = "https://{$domain}.klarna.com/invoices/" . $result[1] . ".pdf";
        return array(
            "klarna_invoice_link" => $link,
            "klarna_invoice_number" => $result[1]
        );
    }

    /**
     * Perform the cancel call
     *
     * @return void
     */
    public function cancel()
    {
        $result = $this->klarna->cancelReservation($this->getTransactionId());
        return array(
            "klarna_cancel_status" => $result,
            "klarna_invoice_number" => $this->getTransactionId()
        );
    }

}
