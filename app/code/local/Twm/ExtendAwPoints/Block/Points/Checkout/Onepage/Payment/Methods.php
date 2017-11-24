<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 21-11-17
 * Time: 17:08
 */
// MageWorx_CustomerCredit_Model_Payment_Method_Customercredit//
class Twm_ExtendAwPoints_Block_Points_Checkout_Onepage_Payment_Methods extends AW_Points_Block_Checkout_Onepage_Payment_Methods
{
    public function getFreePaymentMethod()
    {
        return Mage::getModel('pointsandrewards/method_pointsandrewards');
    }

    public function pointsSectionAvailable()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();
        $quoteData = $quote->getData();
        $grandTotal = $quoteData['grand_total'];

        $minimumOrder = trim(str_replace(',','.', Mage::getStoreConfig('points/general/minimum_points_amount_for_spend')));

        if ($minimumOrder != '' && $grandTotal >= $minimumOrder)
        {
            return parent::pointsSectionAvailable();
        }
        elseif ($minimumOrder != '' && $grandTotal < $minimumOrder)
        {
            return false;
        }
        else
            return true;



    }
}