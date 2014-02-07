<?php
/**
 * File used for handling the Klarna calls
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
 * Klarna_KlarnaPaymentModule_Helper_Gateway_Abstract
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
abstract class Klarna_KlarnaPaymentModule_Helper_Gateway_Abstract
    extends Mage_Core_Helper_Abstract
{

    const ALTCREDTIME = 65536;

    protected $klarna;

    protected $order;

    protected $data;

    protected $locale;

    /**
     * Initialize the gateway helper
     *
     * @param Klarna $klarna The Klarna object to use
     * @param object $order  The Magento order
     * @param array  $data   The payment info
     *
     * @return Klarna_KlarnaPaymentModule_Helper_Gateway_Abstract
     */
    public function init($klarna, $order, $data)
    {
        $this->klarna = $klarna;
        $this->locale = KiTT::locale($this->klarna->getCountryCode());
        $this->order = $order;
        $this->data = $data;
        $this->klarna->setEstoreInfo($this->order->getIncrementId());
    }

    /**
     * Add the extra items that should only be added on the first activation
     *
     * @return void
     */
    private function _addExtras()
    {
        $extraFees = Mage::helper('klarnaPaymentModule/gateway_extras')
            ->init($this->data, $this->order);

        foreach ($extraFees->assemble() as $fee) {
            $this->_addArticle($fee);
        }
    }

    /**
     * Add an article to the goods list and pad it with default values
     *
     * Keys : qty, sku, name, price, tax, discount, flags
     *
     * @param array $array The array to use
     *
     * @return void
     */
    private function _addArticle($array)
    {
        $default = array(
            "qty" => 0,
            "sku" => "",
            "name" => "",
            "price" => 0,
            "tax" => 0,
            "discount" => 0,
            "flags" => KlarnaFlags::NO_FLAG
        );

        //Filter out null values and overwrite the default values
        $values = array_merge($default, array_filter($array));
        $this->klarna->addArticle(
            $values["qty"],
            KiTT_String::encode($values["sku"]),
            KiTT_String::encode($values["name"]),
            $values["price"],
            $values["tax"],
            $values["discount"],
            $values["flags"]
        );
    }

    /**
     * Set the company reference for the purchase
     *
     * @param KlarnaAddr $shipping Klarna shipping address
     * @param KlarnaAddr $billing  Klarna billing address
     *
     * @return void
     */
    private function _setReference($shipping, $billing)
    {
        $reference = null;
        if (array_key_exists("reference", $this->data)) {
            $reference = $this->data["reference"];
        } elseif ($billing->isCompany) {
            $reference = $shipping->getFirstName() . " " . $shipping->getLastName();
        } elseif ($shipping->isCompany) {
            $reference = $billing->getFirstName() . " " . $billing->getLastName();
        }

        if (strlen($reference) == 0) {
            return;
        }
        $reference = html_entity_decode(trim($reference), ENT_COMPAT, 'ISO-8859-1');
        $this->klarna->setReference($reference, "");
        $this->klarna->setComment("Ref:{$reference}");
    }

    /**
     * Set the addresses on the Klarna object
     *
     * @return void
     */
    protected function setAddresses()
    {
        $helper = Mage::helper('klarnaPaymentModule/address');
        $shipping = $helper->toKlarna($this->order->getShippingAddress());
        $billing = $helper->toKlarna($this->order->getBillingAddress());

        $email = "";
        if (array_key_exists("email", $this->data)) {
            $email = $this->data["email"];
        }
        $shipping->setEmail($email);
        $billing->setEmail($email);

        $this->_setReference($shipping, $billing);

        $this->klarna->setAddress(KlarnaFlags::IS_SHIPPING, $shipping);
        $this->klarna->setAddress(KlarnaFlags::IS_BILLING, $billing);
    }

    /**
     * Update the goods list
     *
     * @param array $items The items to add to the goods list
     *
     * @return void
     */
    protected function updateGoodsList($items = null)
    {
        if ($items === null) {
            $items = $this->order->getAllVisibleItems();
        }

        foreach ($items as $item) {
            //For handling the different activation
            $qty = $item->getQtyOrdered(); //Standard
            if (!isset($qty)) {
                $qty = $item->getQty(); //Advanced
            }
            $id = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($id);

            $extras = Mage::helper('klarnaPaymentModule/gateway_extras')
                ->init($this->data, $this->order);

            $taxRate = $extras->getTaxRate($product->getTaxClassId());

            $this->_addArticle(
                array(
                    "qty" => $qty,
                    "sku" => $item->getSku(),
                    "name" => $item->getName(),
                    "price" => $item->getPriceInclTax(),
                    "tax" => $taxRate,
                    "discount" => 0,
                    "flags" => KlarnaFlags::INC_VAT
                )
            );
        }

        //Only add discounts and etc for unactivated orders
        if ($this->order->hasInvoices() <= 1) {
            $this->_addExtras();
        }
    }

    /**
     * Get the Personal Number associated to this purchase
     *
     * @return string
     */
    protected function getPNO()
    {
        $logic = KiTT::countryLogic($this->locale);
        if ($logic->needDateOfBirth()) {
            if ((array_key_exists("birth_day", $this->data))
                && (array_key_exists("birth_month", $this->data))
                && (array_key_exists("birth_year", $this->data))
            ) {
                return $this->data["birth_day"]
                    . $this->data["birth_month"]
                    . $this->data["birth_year"];
            }
        } elseif (array_key_exists("pno", $this->data)
            && strlen($this->data["pno"]) > 0
        ) {
            return $this->data["pno"];
        }
        return "";
    }

    /**
     * Get the gender associated to this purchase
     *
     * @return null|int
     */
    protected function getGender()
    {
        $logic = KiTT::countryLogic($this->locale);
        if ($logic->needGender() && array_key_exists("gender", $this->data)) {
            return $this->data["gender"];
        }
        return null;
    }

    /**
     * Get the payment plan associated to this purchase
     *
     * @return int
     */
    protected function getPaymentPlan()
    {
        if ((array_key_exists("paymentPlan", $this->data))
            && ($this->order->getPayment()->getMethod() !== "klarna_invoice")
        ) {
            return (int)$this->data["paymentPlan"];
        }
        return -1;
    }

    /**
     * Get the transaction id associated to this purchase
     *
     * @return string
     */
    protected function getTransactionId()
    {
        if (array_key_exists("klarna_transaction_id", $this->data)) {
            return $this->data["klarna_transaction_id"];
        }
        throw new KiTT_Exception("No transaction id found");
    }

    /**
     * Abstract function to perform the transaction call
     *
     * @return void
     */
    public abstract function transaction();

    /**
     * Abstract function to perform the activation call
     *
     * @param array $items The items to add
     *
     * @return void
     */
    public abstract function activate($items);

    /**
     * Abstract function to perform the cancel call
     *
     * @return void
     */
    public abstract function cancel();

}
