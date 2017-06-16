<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   2.4.5
 * @build     1677
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Model_Resource_Offline_Item extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * @retun void
     */
    protected function _construct()
    {
        $this->_init('rma/offline_item', 'offline_item_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rma_Model_Offline_Item $object */
        if (!$object->getIsMassDelete()) {
        }
        return parent::_afterLoad($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rma_Model_Offline_Item $object */
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return parent::_beforeSave($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rma_Model_Offline_Item $object */
        if (!$object->getIsMassStatus()) {
        }

        return parent::_afterSave($object);
    }

    /************************/
}
