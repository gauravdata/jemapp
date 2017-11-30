<?php
class AW_Storecredit_Model_Sales_Order_Totals_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('aw_storecredit');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $_result = parent::collect($address);

        $baseTotal = $address->getBaseGrandTotal();
        $total = $address->getGrandTotal();

        $baseTotalStorecreditAmount = 0;
        $totalStorecreditAmount = 0;

        $quote = $address->getQuote();

        if ($baseTotal) {
            $quoteStorecredits = Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit($quote->getId());

            foreach($quoteStorecredits as $quoteStorecredit) {
                $_baseStorecreditAmount = $quoteStorecredit->getStorecreditBalance();
                $_storecreditAmount = $quote->getStore()->roundPrice(
                    $quote->getStore()->convertPrice($quoteStorecredit->getStorecreditBalance())
                );

                $baseStorecreditUsedAmount = $_baseStorecreditAmount;

                if ($_baseStorecreditAmount >= $baseTotal) {
                    $baseStorecreditUsedAmount = $baseTotal;
                }

                $storecreditUsedAmount = $_storecreditAmount;

                if ($_storecreditAmount >= $total) {
                    $storecreditUsedAmount = $total;
                }

                $_baseStorecreditAmount = round($baseStorecreditUsedAmount, 4);
                $_storecreditAmount = round($storecreditUsedAmount, 4);
                $baseTotalStorecreditAmount += $_baseStorecreditAmount;
                $totalStorecreditAmount += $_storecreditAmount;

                Mage::helper('aw_storecredit/totals')
                    ->saveQuoteStorecreditTotals($quoteStorecredit->getLinkId(), $_baseStorecreditAmount, $_storecreditAmount)
                ;
            }
            $address
                ->getQuote()
                ->setBaseAwStorecreditAmountUsed($baseTotalStorecreditAmount)
                ->setAwStorecreditAmountUsed($totalStorecreditAmount)
            ;
            $address
                ->setBaseAwStorecreditAmountUsed($baseTotalStorecreditAmount)
                ->setAwStorecreditAmountUsed($totalStorecreditAmount)
                ->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTotalStorecreditAmount)
                ->setGrandTotal($address->getGrandTotal() - $totalStorecreditAmount)
            ;
        }
        return $_result;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $_result = parent::fetch($address);
        if ( ! ($address->getAwStorecreditAmountUsed() > 0)) {
            return $_result;
        }
        $storecredit = Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit($address->getQuote()->getId());
        $address->addTotal(
            array(
                'code'       => $this->getCode(),
                'title'      => Mage::helper('aw_storecredit')->__('Store Credit'),
                'value'      => -$address->getAwStorecreditAmountUsed(),
                'storecredit' => $storecredit,
            )
        );
        return $_result;
    }
}