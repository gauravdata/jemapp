<?php

class AW_Storecredit_Block_Frontend_Customer_Storecredit_History extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $collection = Mage::getModel('aw_storecredit/history')
            ->getTransactionsCollection()
            ->joinStoreCreditTable()
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('updated_at', 'DESC')
        ;
        $this->setData('collection', $collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock('page/html_pager', 'aw.storecredit.history.pager')
            ->setCollection($this->getCollection());
        $this->setChild('awstorecredit_pager', $pager);
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('awstorecredit_pager');
    }

    public function getTransactions()
    {
        return $this->getCollection();
    }

    public function getAdditionalInfoFromTransaction($transaction)
    {
        $additionalInfo = $transaction->getAdditionalInfo();
        if (!is_array($additionalInfo)
            || !array_key_exists('message_type', $additionalInfo)
            || !array_key_exists('message_data', $additionalInfo)
        ) {
            return '';
        }
        $message = Mage::helper('aw_storecredit')->prepareFrontendMessage($additionalInfo);

        return $message;
    }


    public function getActionFromTransaction($transaction)
    {
        return Mage::getModel('aw_storecredit/source_storecredit_history_action')->getOptionByValue($transaction->getAction());
    }

    public function getBalanceDeltaFromTransaction($transaction)
    {
        $balanceDelta = $transaction->getBalanceDelta();
        $balanceDeltaFormatted = Mage::helper('core')->currency($balanceDelta, true, false);
        if ($balanceDelta < 0) {
            $balanceDeltaFormatted = '<span style="color: #ff0000">' . $balanceDeltaFormatted . '</span>';
        }

        return $balanceDeltaFormatted;
    }


}