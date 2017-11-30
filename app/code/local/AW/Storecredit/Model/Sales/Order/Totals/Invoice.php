<?php
class AW_Storecredit_Model_Sales_Order_Totals_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $_result = parent::collect($invoice);

        $baseTotal = $invoice->getBaseGrandTotal();
        $total = $invoice->getGrandTotal();

        $baseTotalStorecreditAmount = 0;
        $totalStorecreditAmount = 0;
        $invoiceStorecredit = array();

        if (null === $invoice->getId()) {
            $quoteStorecredits = Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit(
                $invoice->getOrder()->getQuoteId()
            );

            foreach($quoteStorecredits as $quoteStorecredit) {
                $_baseStorecreditAmount = $quoteStorecredit->getBaseStorecreditAmount();
                $_storecreditAmount = $quoteStorecredit->getStorecreditAmount();

                $invoices = Mage::helper('aw_storecredit/totals')->getAllInvoicesForStorecredit(
                    $invoice->getOrder()->getId(), $quoteStorecredit->getStorecreditId()
                );
                if (count($invoices) > 0) {
                    foreach ($invoices as $storecreditInvoice) {
                        $_baseStorecreditAmount -= $storecreditInvoice->getBaseStorecreditAmount();
                        $_storecreditAmount -= $storecreditInvoice->getStorecreditAmount();
                    }
                }
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

                $_invoiceStorecredit = new Varien_Object($quoteStorecredit->getData());
                $_invoiceStorecredit
                    ->setBaseStorecreditAmount($_baseStorecreditAmount)
                    ->setStorecreditAmount($_storecreditAmount)
                ;
                array_push($invoiceStorecredit, $_invoiceStorecredit);
            }
        }

        if (null !== $invoice->getId() && $invoice->getAwStorecredit()) {
            $invoiceStorecredits = $invoice->getAwStorecredit();
            foreach($invoiceStorecredits as $invoiceStorecredit){
                $baseTotalStorecreditAmount += $invoiceStorecredit->getBaseStorecreditAmount();
                $totalStorecreditAmount += $invoiceStorecredit->getStorecreditAmount();
            }

        }

        $invoice
            ->setAwStorecredit($invoiceStorecredit)
            ->setBaseAwStorecreditAmountUsed($baseTotalStorecreditAmount)
            ->setAwStorecreditAmountUsed($totalStorecreditAmount)
            ->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalStorecreditAmount)
            ->setGrandTotal($invoice->getGrandTotal() - $totalStorecreditAmount)
        ;
        return $_result;
    }
}