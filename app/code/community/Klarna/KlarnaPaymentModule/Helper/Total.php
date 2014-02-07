<?php
/**
 * File used in order add a invoice fee to Order Total Blocks
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
 * Helper class to add invoice fees to order totals
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Total extends Mage_Core_Helper_Abstract
{

    /**
     * Add a invoice fee to a provided Order Total Block
     *
     * @param object $block The order total block to use
     *
     * @return object
     */
    public function addToBlock($block)
    {
        $order = $block->getOrder();
        $info = $order->getPayment()->getMethodInstance()->getInfoInstance();
        $storeId = Mage::app()->getStore()->getId();
        $vatOption = Mage::getStoreConfig("tax/sales_display/price", $storeId);

        $invoiceFee = $info->getAdditionalInformation('invoice_fee');
        $baseInvoiceFee = $info->getAdditionalInformation('base_invoice_fee');
        $invoiceFeeExVat = $info->getAdditionalInformation(
            'invoice_fee_exluding_vat'
        );
        $baseInvoiceFeeExVat = $info->getAdditionalInformation(
            'base_invoice_fee_exluding_vat'
        );

        Mage::helper('klarnaPaymentModule/api')->loadConfig();
        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createOrderLocale($order);
        $translator = KiTT::translator($locale);

        /**
         * 1 : Show exluding tax
         * 2 : Show including tax
         * 3 : Show both
         */
        if (($vatOption === '1')
            || ($vatOption === '3')
        ) {
            $fee = new Varien_Object();
            $fee->setCode('invoice_fee_excl');
            $label = $translator->translate('ot_klarna_title');
            if ($vatOption == '3') {
                $label .= ' (Excl.Tax)';
            }
            $fee->setLabel($label);
            $fee->setBaseValue($baseInvoiceFeeExVat);
            $fee->setValue($invoiceFeeExVat);
            $block->addTotalBefore($fee, 'shipping');
        }
        if (($vatOption === '2')
            || ($vatOption === '3')
        ) {
            $fee = new Varien_Object();
            $fee->setCode('invoice_fee_incl');
            $label = $translator->translate('ot_klarna_title');
            if ($vatOption == '3') {
                $label .= ' (Incl.Tax)';
            }
            $fee->setLabel($label);
            $fee->setBaseValue($baseInvoiceFee);
            $fee->setValue($invoiceFee);
            $block->addTotalBefore($fee, 'shipping');
        }

        return $block;
    }

}
