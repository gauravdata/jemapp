<?php
/**
 * File containing helper for extra goods items, fees and discount.
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
 * Klarna_KlarnaPaymentModule_Helper_Gateway_Extras
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Gateway_Extras
    extends Mage_Core_Helper_Abstract
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @var  Mage_Varian_Order
     */
    protected $order;

    /**
     * @var Mage_Varian_Quote
     */
    private $_quote;

    /**
     * @var KiTT_Translator
     */
    private $_translator;

    /**
     * @var array
     */
    private $_extras;

    /**
     * Entry point.
     *
     * @param array  $data  data array
     * @param object $order paramname description
     *
     * @return void No return value
     */
    public function init($data, $order)
    {
        $this->data = $data;
        $this->order = $order;
        $this->_quote = $order->getQuote();
        $this->_extras = array();

        return $this;
    }

    /**
     * Returns the tax rate
     *
     * @param int $taxClass The tax class to get the rate for
     *
     * @return double The tax rate
     */
    public function getTaxRate($taxClass)
    {
        // Load the customer so we can retrevice the correct tax class id
        $customer = Mage::getModel('customer/customer')
            ->load($this->order->getCustomerId());
        $calculation = Mage::getSingleton('tax/calculation');
        $request = $calculation->getRateRequest(
            $this->order->getShippingAddress(),
            $this->order->getBillingAddress(),
            $customer->getTaxClassId(),
            $this->order->getStore()
        );
        return $calculation->getRate($request->setProductClassId($taxClass));
    }

    /**
     * Assemble all possible fees and discounts.
     *
     * @return array Array of fee
     */
    public function assemble()
    {
        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createOrderLocale($this->order);
        $this->_translator = KiTT::translator($locale);

        $this->_addInvoiceFee();

        $this->_addShippingFee();

        $this->_addGiftCard();

        $this->_addCustomerBalance();

        $this->_addRewardCurrency();

        $this->_addDiscount();

        $this->_addGiftWrapPrice();

        $this->_addGiftWrapItemPrice();

        $this->_addGwPrintedCardPrice();

        return $this->_extras;
    }

    /**
     * Add the Gift Wrap Order price to the goods list
     *
     * @return void
     */
    private function _addGiftWrapPrice()
    {
        if ($this->order->getGwPrice() <= 0) {
            return;
        }

        $price = $this->order->getGwPrice();
        $tax = $this->order->getGwTaxAmount();

        $name = Mage::helper("enterprise_giftwrapping")
            ->__("Gift Wrapping for Order");
        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "gw_order",
            "name" => $name,
            "price" => $price + $tax,
        );
    }

    /**
     * Add the Gift Wrap Item price to the goods list
     *
     * @return void
     */
    private function _addGiftWrapItemPrice()
    {
        if ($this->order->getGwItemsPrice() <= 0) {
            return;
        }

        $price = $this->order->getGwItemsPrice();
        $tax = $this->order->getGwItemsTaxAmount();

        $name = Mage::helper("enterprise_giftwrapping")
            ->__("Gift Wrapping for Items");

        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "gw_items",
            "name" => $name,
            "price" => $price + $tax
        );
    }

    /**
     * Add the Gift Wrap Printed Card to the goods list
     *
     * @return void
     */
    private function _addGwPrintedCardPrice()
    {
        if ($this->order->getGwPrintedCardPrice() <= 0) {
            return;
        }

        $price = $this->order->getGwPrintedCardPrice();
        $tax = $this->order->getGwPrintedCardTaxAmount();

        $name = Mage::helper("enterprise_giftwrapping")->__("Printed Card");
        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "gw_printed_card",
            "name" => $name,
            "price" => $price + $tax
        );
    }

    /**
     * Add the gift card amount to the goods list
     *
     * @return void
     */
    private function _addGiftCard()
    {
        if ($this->order->getGiftCardsAmount() <= 0) {
            return;
        }

        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "gift_card",
            "name" => $this->_translator->translate("gift_card"),
            "price" => ($this->order->getGiftCardsAmount() * -1)
        );
    }

    /**
     * Add the customer balance to the goods list
     *
     * @return void
     */
    private function _addCustomerBalance()
    {
        if ($this->order->getCustomerBalanceAmount() <= 0) {
            return;
        }
        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "customer_balance",
            "name" => $this->_translator->translate("customer_balance"),
            "price" => ($this->order->getCustomerBalanceAmount() * -1)
        );
    }

    /**
     * Add a reward currency amount to the goods list
     *
     * @return void
     */
    private function _addRewardCurrency()
    {
        if ($this->order->getRewardCurrencyAmount() <= 0) {
            return;
        }
        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "reward_currency",
            "name" => $this->_translator->translate("reward_currency"),
            "price" => ($this->order->getRewardCurrencyAmount() * -1)
        );
    }

    /**
     * Get the invoice fee
     *
     * @return float
     */
    private function _getFee()
    {
        if (isset($this->_quote)) {
            return $this->_quote->getInvoiceFee();
        }
        if (array_key_exists("invoice_fee", $this->data)) {
            return $this->data["invoice_fee"];
        }
        return 0;
    }

    /**
     * Get the invoice fee tax rate
     *
     * @return float
     */
    private function _getFeeRate()
    {
        if (isset($this->_quote)) {
            return $this->_quote->getInvoiceFeeRate();
        }
        if (array_key_exists("invoice_fee_rate", $this->data)) {
            return $this->data["invoice_fee_rate"];
        }
        return 0;
    }

    /**
     * Add the invoice fee to the goods list
     *
     * @return void
     */
    private function _addInvoiceFee()
    {
        if ($this->order->getPayment()->getMethod() !== "klarna_invoice") {
            return;
        }

        $this->_extras[] = array(
            "qty" => 1,
            "sku" => "invoice_fee",
            "name" => $this->_translator->translate("ot_klarna_title"),
            "price" => $this->_getFee(),
            "tax" => $this->_getFeeRate(),
            "flags" => KlarnaFlags::INC_VAT | KlarnaFlags::IS_HANDLING
        );
    }

    /**
     * Add the shipment fee to the goods list
     *
     * @return void
     */
    private function _addShippingFee()
    {
        if ($this->order->getShippingInclTax() <= 0) {
            return;
        }
        $taxClass = Mage::getStoreConfig('tax/classes/shipping_tax_class');

        $this->_extras[] = array(
            "qty" => 1,
            "sku" => $this->order->getShippingMethod(),
            "name" => $this->order->getShippingDescription(),
            "price" => $this->order->getShippingInclTax(),
            "tax" => $this->getTaxRate($taxClass),
            "flags" => KlarnaFlags::INC_VAT | KlarnaFlags::IS_SHIPMENT
        );
    }

    /**
     * Add the discount to the goods list
     *
     * @return void
     */
    private function _addDiscount()
    {
        if ($this->order->getDiscountAmount() >= 0) {
            return;
        }

        $amouExtrant = $this->order->getDiscountAmount();
        $applyAfter = Mage::helper('tax')->applyTaxAfterDiscount(
            $this->order->getStoreId()
        );
/*
        if ($applyAfter == true) {
            //With this setting active the discount will not have the correct
            //value. We need to take each respective products rate and calculate
            //a new value.
            $amount = 0;
            foreach ($this->order->getAllVisibleItems() as $product) {
                $rate = $product->getTaxPercent();
                $newAmount = $product->getDiscountAmount() * (($rate / 100 ) + 1);
                $amount -= $newAmount;
            }
            //If the discount also extends to shipping
            $shippingDiscount = $this->order->getShippingDiscountAmount();
            if ($shippingDiscount) {
                $taxClass = Mage::getStoreConfig('tax/classes/shipping_tax_class');
                $rate = $this->getTaxRate($taxClass);
                $newAmount = $shippingDiscount * (($rate / 100 ) + 1);
                $amount -= $newAmount;
            }
        }
*/

        $desc = $this->order->getDiscountDescription();

        $this->_extras[] = array(
            "qty" => 1,
            "sku" => $desc,
            "name" => Mage::helper('sales')->__('Discount (%s)', $desc),
            "price" => $amount,
	        "flags" => KlarnaFlags::INC_VAT
        );
    }
}
