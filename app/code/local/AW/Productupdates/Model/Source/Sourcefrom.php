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


class AW_Productupdates_Model_Source_Sourcefrom
{
    const CATALOG_PRICE_RULE = 1;
    const PRODUCT_CHANGE = 2;
    const ATTRIBUTES_CHANGE = 3;
    const PRODUCT_PIRCE_INDEX = 4;
    const INVENTORY_INDEX = 5;

    public function getAllowedTypes()
    {
        return array(
            'catalog_price_changed'     => self::CATALOG_PRICE_RULE,
            'product_price_changed'     => self::PRODUCT_CHANGE,
            'attribute_price_changed'   => self::ATTRIBUTES_CHANGE,
            'product_price_index'       => self::PRODUCT_PIRCE_INDEX,
            'inventory_index'           => self::INVENTORY_INDEX
        );
    }

}