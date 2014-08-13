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
 * @package    AW_Previousnext
 * @version    1.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Previousnext_Model_Observer
{
    public function memorizeProductCollection($observer)
    {
        try {
            $productIds = array();
            /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
            $productCollection = $observer->getCollection();
            $productCollection->load();
            $productCollection->clear();

            $productCollectionClone = clone $productCollection;
            $productCollectionClone->setPageSize(1000);
            $productCollectionClone->setCurPage(1);
            $productCollectionClone->clear();

            foreach($productCollectionClone as $product) {
                $productIds[] = $product->getId();
            }
            Mage::getSingleton('core/session')->setData('aw_prevnext_pids', $productIds);

            $request = Mage::app()->getRequest();
            Mage::getSingleton('core/session')->setData('aw_prevnext_req', $request);

            if (Mage::registry('current_category')) {
                $categoryId = (string)Mage::registry('current_category')->getData('entity_id');
            } else {
                $categoryId = null;
            }
            Mage::getSingleton('core/session')->setData('aw_prevnext_cat', $categoryId);
        } catch (Exception $e) {
            // 
        }
    }
}
