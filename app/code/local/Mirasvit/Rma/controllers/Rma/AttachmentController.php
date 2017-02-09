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


class Mirasvit_Rma_Rma_AttachmentController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return void
     */
    public function downloadAction()
    {
        $uid         = $this->getRequest()->getParam('id');
        $attachments = Mage::getModel('rma/attachment')->getCollection()
            ->addFieldToFilter('external_id', $uid);
        if (!$attachments->count()) {
            die('Permission error');
        }
        $attachment = $attachments->getFirstItem();
        header("Content-Disposition: attachment; filename=\"{$attachment->getName()}\"");
        header("Content-length: {$attachment->getSize()}");
        header("Content-type: {$attachment->getType()}");
        echo $attachment->getBody();
        die;
    }
}
