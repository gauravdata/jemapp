<?php
class AW_Storecredit_Block_Adminhtml_Widget_Grid_Column_Filter_Date
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date
{
    protected function _convertDate($date, $locale)
    {
        $dateObj = $this->getLocale()->date($date, null, $locale, false);
        return $dateObj;
    }
}