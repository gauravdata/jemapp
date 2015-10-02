<?php

class Shopworks_Billink_Model_Fee_Quote_Address_Total_Billink
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Code for payment method check
     */
    protected $_paymentMethodCode = null;

    /**
     * @var Shopworks_Billink_Helper_BillinkFee
     */
    private $_feeHelper;
    
    public function __construct()
    {
        $this->setCode(Shopworks_Billink_Model_Payment_Method::TOTAL_FEE_CODE);
        $this->_paymentMethodCode = Shopworks_Billink_Model_Payment_Method::PAYMENT_METHOD_BILLINK_CODE;

        $this->_feeHelper = Mage::helper('billink/BillinkFee');
    }
    
    /**
     * Add billink totals information to address object
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (Mage::getStoreConfig("payment/{$this->_paymentMethodCode}/fee_enabled", $address->getQuote()->getStore()))
        {
            parent::fetch($address);
            $amount = $address->getTotalAmount($this->getCode());
            
            if ($amount != 0)
            {
                $address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => $this->getLabel(),
                    'value' => $amount
                ));
            }
        }

        return $this;
    }
    
    /**
     * Collect totals information for billink costs
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        
        //only collect if items are availible
        $items = $this->_getAddressItems($address);
        if (!count($items))
        {
            return $this;
        }

        //get the quote and store for calculations
        $quote = $address->getQuote();
        $store = $quote->getStore();

        if (!Mage::getStoreConfig("payment/{$this->_paymentMethodCode}/fee_enabled", $store))
        {
            return $this;
        }
        
        //check if the current payment method is billink
        $paymentMethod = $address->getQuote()->getPayment()->getMethod();
        if ($paymentMethod && $paymentMethod == $this->_paymentMethodCode)
        {
            $inclTax = Mage::getStoreConfig("payment/{$this->_paymentMethodCode}/fee_includes_tax", $store);

            //get the fee to apply
            $amount = $this->_feeHelper->getBillinkFeeFromConfig($this->_feeHelper->getQuoteTotalInclTax($quote), $store);
            $amount = $quote->getStore()->roundPrice($amount);
            $this->_setAmount($amount)->_setBaseAmount($amount);

            /** @var Shopworks_Billink_Helper_BillinkFee $helper */
            $feeTax = $this->_feeHelper->getTax($this->_feeHelper->getQuoteTotalInclTax($quote), $quote->getShippingAddress(), $quote->getBillingAddress(), $quote->getCustomer(), $store);
            $this->_saveAppliedTaxes(
                $address,
                $feeTax->applied,
                $feeTax->tax,
                $feeTax->baseTax,
                $feeTax->rate
            );

            //add fee values to address, subtract tax if fee price is including tax
            $address->setBillinkFee($feeTax->feeExclTax);
            $address->setBaseBillinkFee($feeTax->feeExclBaseTax);
            $address->setBillinkFeeInclTax($feeTax->feeExclTax + $feeTax->tax);
            $address->setBaseBillinkFeeInclTax($feeTax->feeExclBaseTax + $feeTax->baseTax);

            $address->setBillinkFeeTax($feeTax->tax);
            $address->setBaseBillinkFeeTax($feeTax->baseTax);
            
            //set tax fieds to address
            $address->addTotalAmount('tax', $feeTax->tax);
            $address->addBaseTotalAmount('tax', $feeTax->baseTax);
            
            //add tax to total if fee price is excluding tax
            if($inclTax == 1) 
            {
                $address->setGrandTotal($address->getGrandTotal() - $feeTax->tax);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $feeTax->baseTax);
            }
        }

        return $this;
    }

    /**
     * Apply taxes for order fee
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param array $applied taxes
     * @param float $amount
     * @param float $baseAmount
     * @param float $rate
     */
    protected function _saveAppliedTaxes(Mage_Sales_Model_Quote_Address $address, $applied, $amount, $baseAmount, $rate)
    {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $process = count($previouslyAppliedTaxes);

        foreach ($applied as $row) 
        {
            if (!isset($previouslyAppliedTaxes[$row['id']])) {
                $row['process'] = $process;
                $row['amount'] = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if (!is_null($row['percent'])) 
            {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount = $amount/$rate*$row['percent'];
                $baseAppliedAmount = $baseAmount/$rate*$row['percent'];
            } 
            else 
            {
                $appliedAmount = 0;
                $baseAppliedAmount = 0;
                foreach ($row['rates'] as $rate) 
                {
                    $appliedAmount += $rate['amount'];
                    $baseAppliedAmount += $rate['base_amount'];
                }
            }

            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) 
            {
                $previouslyAppliedTaxes[$row['id']]['amount'] += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount'] += $baseAppliedAmount;
            } 
            else 
            {
                unset($previouslyAppliedTaxes[$row['id']]);
            }
        }

        $address->setAppliedTaxes($previouslyAppliedTaxes);
    }

    /**
     * Get label (displayed on frontend)
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::getStoreConfig('payment/billink/fee_label');
    }
    
}
