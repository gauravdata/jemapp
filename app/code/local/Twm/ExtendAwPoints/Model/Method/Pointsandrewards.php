<?php

class Twm_ExtendAwPoints_Model_Method_Pointsandrewards extends Mage_Payment_Model_Method_Abstract
{
    /**
     * XML Paths for configuration constants
     */
    const XML_PATH_PAYMENT_POINTSANDREWARDS_ACTIVE = 'payment/pointsandrewards/active';
    const XML_PATH_PAYMENT_POINTSANDREWARDS_ORDER_STATUS = 'payment/pointsandrewards/order_status';
    const XML_PATH_PAYMENT_POINTSANDREWARDS_PAYMENT_ACTION = 'payment/pointsandrewards/payment_action';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'pointsandrewards';

    /**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote|null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && !empty($quote)
            && Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) == 0;
    }

    /**
     * Get config payment action, do nothing if status is pending
     *
     * @return string|null
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('order_status') == 'pending' ? null : parent::getConfigPaymentAction();
    }
}