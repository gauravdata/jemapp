<?php
class AW_Storecredit_Helper_Totals extends Mage_Core_Helper_Abstract
{
    public function getQuoteStoreCredit($quoteId = null)
    {
        if (null === $quoteId) {
            $quoteId = $this->getQuote()->getId();
        }
        $quoteStoreCreditCollection = Mage::getModel('aw_storecredit/quote_storecredit')->getCollection();
        $quoteStoreCreditCollection
            ->joinStorecreditTable()
            ->setFilterByQuoteId($quoteId)
        ;
        return $quoteStoreCreditCollection->getItems();
    }

    public function getRefundedStoreCredit($orderId)
    {
        $refundedStoreCreditCollection = Mage::getModel('aw_storecredit/order_refunded_storecredit')->getCollection();
        $refundedStoreCreditCollection
            ->joinStorecreditTable()
            ->setFilterByOrderId($orderId)
        ;
        return $refundedStoreCreditCollection->getItems();
    }

    public function getInvoiceStoreCredit($invoiceId)
    {
        $invoiceStoreCreditCollection = Mage::getModel('aw_storecredit/order_invoice_storecredit')->getCollection();
        $invoiceStoreCreditCollection
            ->joinStorecreditTable()
            ->setFilterByInvoiceId($invoiceId)
        ;
        return $invoiceStoreCreditCollection->getItems();
    }

    public function getCreditmemoStoreCredit($creditmemoId)
    {
        $creditmemoStoreCreditCollection = Mage::getModel('aw_storecredit/order_creditmemo_storecredit')->getCollection();
        $creditmemoStoreCreditCollection
            ->joinStorecreditTable()
            ->setFilterByCreditmemoId($creditmemoId)
        ;
        return $creditmemoStoreCreditCollection->getItems();
    }

    public function getAllCreditmemoForStoreCredit($orderId, $storecreditId)
    {
        $creditmemoIds = Mage::getResourceModel('sales/order_creditmemo_collection')
            ->setOrderFilter($orderId)
            ->getAllIds()
        ;

        $creditmemoStoreCreditCollection = Mage::getModel('aw_storecredit/order_creditmemo_storecredit')->getCollection();
        $creditmemoStoreCreditCollection
            ->joinStorecreditTable()
            ->setFilterByCreditmemoIds($creditmemoIds)
            ->setFilterByStorecreditId($storecreditId)
        ;
        return $creditmemoStoreCreditCollection->getItems();
    }

    public function getAllInvoicesForStorecredit($orderId, $storecreditId)
    {
        $invoiceIds = Mage::getResourceModel('sales/order_invoice_collection')
            ->setOrderFilter($orderId)
            ->getAllIds()
        ;

        $invoiceStorecreditCollection = Mage::getModel('aw_storecredit/order_invoice_storecredit')->getCollection();
        $invoiceStorecreditCollection
            ->joinStorecreditTable()
            ->setFilterByInvoiceIds($invoiceIds)
            ->setFilterByStorecreditId($storecreditId)
        ;
        return $invoiceStorecreditCollection->getItems();
    }

    public function getInvoicedStorecreditByOrderId($orderId)
    {
        $invoiceIds = Mage::getResourceModel('sales/order_invoice_collection')
            ->setOrderFilter($orderId)
            ->getAllIds()
        ;

        $invoiceStorecreditCollection = Mage::getModel('aw_storecredit/order_invoice_storecredit')->getCollection();
        $invoiceStorecreditCollection
            ->joinStorecreditTable()
            ->addSumBaseAmountToFilter()
            ->addSumAmountToFilter()
            ->groupBy('storecredit_id')
            ->setFilterByInvoiceIds($invoiceIds)
        ;
        return $invoiceStorecreditCollection->getItems();
    }

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function saveQuoteStoreCreditTotals($linkId, $baseAmount, $amount)
    {
        $quoteStoreCreditModel = Mage::getModel('aw_storecredit/quote_storecredit')->load($linkId);
        if (null !== $quoteStoreCreditModel) {
            $quoteStoreCreditModel
                ->setBaseStorecreditAmount($baseAmount)
                ->setStorecreditAmount($amount)
                ->save()
            ;
        }
        return $this;
    }

    public function addStoreCreditToQuote(AW_Storecredit_Model_Storecredit $storecredit, $quote = null)
    {
        if (null === $quote) {
            $quote = $this->getQuote();
        }

        Mage::getModel('aw_storecredit/quote_storecredit')
            ->setQuoteEntityId($quote->getId())
            ->setStorecreditId($storecredit->getId())
            ->save()
        ;

        return $this;
    }

    public function removeStoreCreditFromQuote($storecreditId, $quote = null)
    {
        if (null === $quote) {
            $quote = $this->getQuote();
        }
        $quoteStorecreditItems = $this->getQuoteStoreCredit($quote->getId());

        foreach ($quoteStorecreditItems as $storecredit) {
            if ($storecredit->getStorecreditId() == $storecreditId) {
                $storecredit->delete();
                break;
            }
        }
        return $this;
    }
}