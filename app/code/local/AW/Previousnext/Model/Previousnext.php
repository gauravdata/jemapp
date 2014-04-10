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
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Previousnext_Model_Previousnext extends Mage_Core_Model_Abstract
{
    const LOOP = 'previousnext/general/loopproducts';
    private $_prev = 0;
    private $_next = 0;

    protected function _construct()
    {
        $loop = Mage::getStoreConfig(self::LOOP);
        $array = $this->_getDataArray();
        $cnt = count($array);

        if ($cnt > 1) {
            $pos = array_search(Mage::app()->getRequest()->getParam('id'), $array);
            $this->_prev = ($pos == 0) ? ($loop ? $array[$cnt - 1] : 0) : $array[$pos - 1];
            $this->_next = ($pos == ($cnt - 1)) ? ($loop ? $array[0] : 0) : $array[$pos + 1];
        }
    }

    public function getPrevID()
    {
        return (int)$this->_prev;
    }

    public function getNextID()
    {
        return (int)$this->_next;
    }

    private function _getDataArray()
    {
        $results = array();
        $cat = Mage::getSingleton('core/session')->getData('aw_prevnext_cat');
        $category = Mage::registry('current_category');
        if ($category) {
            $categoryId = $category->getData('entity_id');
            if ($cat === $categoryId) {
                $select = Mage::getSingleton('core/session')->getData('aw_prevnext_sql');
            } else {
                try {
                    $productCollection = Mage::getModel('catalog/category')->load($categoryId)->getProductCollection();
                    Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
                    Mage::getSingleton('catalog/product_visibility')
                        ->addVisibleInCatalogFilterToCollection($productCollection)
                    ;
                    $select = $productCollection->getSelect();
                    $select->reset(Zend_Db_Select::LIMIT_COUNT);
                    $select->reset(Zend_Db_Select::LIMIT_OFFSET);
                } catch (Exception $e) {
                    //
                }
            }
            if ($select) {
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $rows = $readConnection->fetchAll((string)$select);
                foreach ($rows as $item) {
                    $results[] = $item['entity_id'];
                }
            }
        }
        return $results;
    }

}

