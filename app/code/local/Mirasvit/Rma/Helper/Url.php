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
 * @version   2.4.0
 * @build     1607
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Helper_Url
{
    /**
     * @return string
     */
    public function getNewRmaUrl()
    {
        return Mage::getUrl('rma/rma_new/step1');
    }

    /**
     * @return string
     */
    public function getRmaListUrl()
    {
        return Mage::getUrl('rma/rma');
    }

    /**
     * @return string
     */
    public function getGuestRmaListUrl()
    {
        return Mage::getUrl('rma/guest/list');
    }

    /**
     * @return string
     */
    public function getGuestRmaUrl()
    {
        return Mage::getUrl('rma/guest/guest');
    }

    /**
     * @return string
     */
    public function getGuestOfflineRmaUrl()
    {
        return Mage::getUrl('rma/guest/offline');
    }

    /**
     * @param Mirasvit_Rma_Model_Rma $rma
     *
     * @return string
     */
    public function getGuestRmaViewUrl($rma)
    {
        return Mage::getUrl('rma/guest/view', array('guest_id' => $rma->getGuestId(), '_store' => $rma->getStoreId()));
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getRmaViewUrl($id)
    {
        return Mage::getUrl('rma/rma/view', array('id' => $id));
    }
}
