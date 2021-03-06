<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2014 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
abstract class TIG_Buckaroo3Extended_Model_PaymentFee_Quote_Address_Total_Fee_Abstract extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    /**
     * Xpath to the Buckaroo fee setting.
     */
    const XPATH_BUCKAROO_FEE = 'buckaroo/%s/payment_fee';

    /**
     * Xpath to Buckaroo fee tax class.
     */
    const XPATH_BUCKAROO_TAX_CLASS = 'tax/classes/buckaroo_fee';

    /**
     * Xpath to the Buckaroo fee including tax setting.
     */
    const XPATH_BUCKAROO_FEE_INCLUDING_TAX = 'tax/calculation/buckaroo_fee_including_tax';

    /**
     * Xpath to the fee_percentage_mode setting.
     */
    const XPATH_BUCKAROO_FEE_PERCENTAGE_MODE = 'buckaroo/buckaroo3extended_advanced/fee_percentage_mode';

    /**
     * @var string
     */
    protected $_totalCode;

    /**
     * @var boolean
     */
    protected $_feeIsInclTax = null;

    /**
     * Constructor method.
     *
     * Sets several class variables.
     */
    public function __construct()
    {
        $this->setCode($this->_totalCode);
        $this->setTaxCalculation(Mage::getSingleton('tax/calculation'));

        $this->_helper = Mage::helper('tax');
        $this->_config = Mage::getSingleton('tax/config');
        $this->_weeeHelper = Mage::helper('weee');
    }

    /**
     * @return Mage_Tax_Model_Calculation
     */
    public function getTaxCalculation()
    {
        $taxCalculation = $this->_calculator;
        if ($taxCalculation) {
            return $taxCalculation;
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');

        $this->setTaxCalculation($taxCalculation);
        return $taxCalculation;
    }

    /**
     * @param Mage_Tax_Model_Calculation $taxCalculation
     *
     * @return $this
     */
    public function setTaxCalculation(Mage_Tax_Model_Calculation $taxCalculation)
    {
        $this->_calculator = $taxCalculation;

        return $this;
    }

    /**
     * Get whether the Buckaroo fee is incl. tax.
     *
     * @param int|Mage_Core_Model_Store|null $store
     *
     * @return bool
     */
    public function getFeeIsInclTax($store = null)
    {
        if (null !== $this->_feeIsInclTax) {
            return $this->_feeIsInclTax;
        }
        
        if (is_null($store)) {
            $storeId = Mage::app()->getStore()->getId();
        } elseif ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        } else {
            $storeId = $store;
        }

        $feeIsInclTax = Mage::getStoreConfigFlag(self::XPATH_BUCKAROO_FEE_INCLUDING_TAX, $storeId);
        
        $this->_feeIsInclTax = $feeIsInclTax;
        return $feeIsInclTax;
    }

    /**
     * Get the tax request object for the current quote.
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return bool|Varien_Object
     */
    protected function _getBuckarooFeeTaxRequest(Mage_Sales_Model_Quote $quote)
    {
        $store = $quote->getStore();
        $codTaxClass      = Mage::getStoreConfig(self::XPATH_BUCKAROO_TAX_CLASS, $store);

        /**
         * If no tax class is configured for the Buckaroo fee, there is no tax to be calculated.
         */
        if (!$codTaxClass) {
            return false;
        }

        $taxCalculation   = $this->getTaxCalculation();
        $customerTaxClass = $quote->getCustomerTaxClassId();
        $shippingAddress  = $quote->getShippingAddress();
        $billingAddress   = $quote->getBillingAddress();

        $request = $taxCalculation->getRateRequest(
            $shippingAddress,
            $billingAddress,
            $customerTaxClass,
            $store
        );

        $request->setProductClassId($codTaxClass);

        return $request;
    }

    /**
     * Get the tax rate based on the previously created tax request.
     *
     * @param Varien_Object $request
     *
     * @return float
     */
    protected function _getBuckarooFeeTaxRate($request)
    {
        $rate = $this->getTaxCalculation()->getRate($request);

        return $rate;
    }

    /**
     * Get the fee tax based on the shipping address and tax rate.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param float                          $taxRate
     * @param float|null                     $fee
     * @param boolean                        $isInclTax
     *
     * @return float
     */
    protected function _getBuckarooFeeTax($address, $taxRate, $fee = null, $isInclTax = false)
    {
        if (is_null($fee)) {
            $fee = (float) $address->getBuckarooFee();
        }

        $taxCalculation = $this->getTaxCalculation();

        $feeTax = $taxCalculation->calcTaxAmount(
            $fee,
            $taxRate,
            $isInclTax,
            false
        );

        return $feeTax;
    }

    /**
     * Get the base fee tax based on the shipping address and tax rate.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param float                          $taxRate
     * @param float|null                     $fee
     * @param boolean                        $isInclTax
     *
     * @return float
     */
    protected function _getBaseBuckarooFeeTax($address, $taxRate, $fee = null, $isInclTax = false)
    {
        if (is_null($fee)) {
            $fee = (float) $address->getBaseBuckarooFee();
        }

        $taxCalculation = $this->getTaxCalculation();

        $baseFeeTax = $taxCalculation->calcTaxAmount(
            $fee,
            $taxRate,
            $isInclTax,
            false
        );

        return $baseFeeTax;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing totals collect sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        return $config;
    }
}