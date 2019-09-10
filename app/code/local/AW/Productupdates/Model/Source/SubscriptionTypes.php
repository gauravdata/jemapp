<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Productupdates_Model_Source_SubscriptionTypes
{
   const WAITING_PRICE_CHANGE = 1;
   const WAITING_STOCK_CHANGE = 2;
   const GENERAL_SUBSCRIPTION_TYPE = 3;
    
   public function getAllowedTypes()
   {
        return array(
           'price_changed'  => self::WAITING_PRICE_CHANGE,
           'stock_changed'  => self::WAITING_STOCK_CHANGE,
           'general_send'   => self::GENERAL_SUBSCRIPTION_TYPE
        );
   }
   
}