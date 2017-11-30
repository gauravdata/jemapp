<?php
class AW_Storecredit_Block_Frontend_Sales_Order_Totals_Storecredit extends Mage_Core_Block_Template
{
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function initTotals()
    {
        if ($this->getSource() instanceof Mage_Sales_Model_Order && null === $this->getSource()->getAwStorecredit()) {
            $quoteStorecreditItems = Mage::helper('aw_storecredit/totals')
                ->getQuoteStoreCredit($this->getSource()->getQuoteId())
            ;

            if ($quoteStorecreditItems) {
                $this->getSource()->setAwStorecredit($quoteStorecreditItems);
            }
        }

        if ($this->getSource()->getAwStorecredit()) {
            $storecredits = $this->getSource()->getAwStorecredit();
            foreach($storecredits as $storecredit) {
                $this->getParentBlock()->addTotal(
                    new Varien_Object(
                        array(
                             'code'   => 'aw_storecredit_' . $storecredit->getStorecreditId(),
                             'strong' => false,
                             'label'  => $this->__('Store Credit'),
                             'value'  => -$storecredit->getStorecreditAmount(),
                        )
                    ),
                    'tax'
                );
            }
        }
        return $this;
    }
}