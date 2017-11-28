<?php

class Twm_ExtendAwPoints_Helper_Data
{
    public function canGetPoints()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();
        $quoteData = $quote->getData();
        $grandTotal = $quoteData['grand_total'];

        $minimumOrder = trim(str_replace(',','.', Mage::getStoreConfig('points/general/minimum_points_amount_for_spend')));

        if ($minimumOrder != '' && $grandTotal >= $minimumOrder)
            return true;
        elseif ($minimumOrder != '' && $grandTotal < $minimumOrder)
            return false;
        else
            return true;
    }
}