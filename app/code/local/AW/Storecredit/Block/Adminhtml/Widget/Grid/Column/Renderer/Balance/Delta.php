<?php
class AW_Storecredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Balance_Delta
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
{
    public function render(Varien_Object $row)
    {
        if (null === $row->getData('balance_delta')) {
            return '';
        }

        $currencyCode = $this->_getCurrencyCode($row);

        if (!$currencyCode) {
            return '';
        }
        $balanceDelta = $row->getData('balance_delta');
        $balanceDelta = floatval($balanceDelta) * $this->_getRate($row);
        $balanceDelta = sprintf("%f", $balanceDelta);
        $balanceDelta = Mage::app()->getLocale()->currency($currencyCode)->toCurrency($balanceDelta);

        if ($row->getData('balance_delta') < 0) {
            $balanceDelta = '<span style="color: #ff0000">' . $balanceDelta . '</span>';
        }

        return $balanceDelta;
    }
}