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



class Mirasvit_Rma_Helper_Comment_Create
{
    /**
     * Save comment function for frontend.
     *
     * @param Mirasvit_Rma_Model_Rma $rma
     * @param array                  $post
     *
     * @throws Mage_Core_Exception
     */
    public function createCommentFromPost($rma, $post)
    {
        $comment = false;
        if (isset($post['comment'])) {
            $comment = $post['comment'];
        }
        unset($post['id']);
        unset($post['comment']);
        $fields = array();
        foreach ($post as $code => $value) {
            if (!$value) {
                continue;
            }
            $field = Mage::getModel('rma/field')->getCollection()
                ->addFieldToFilter('code', $code)
                ->getFirstItem();
            if ($field->getId()) {
                $fields[] = "{$field->getName()}: {$value}";
                $rma->setData($code, $value);
            }
        }
        if (count($fields)) {
            if ($comment) {
                $comment .= "\n";
            }
            $comment .= implode("\n", $fields);
        }
        if (trim($comment) == '' && !Mage::helper('mstcore/attachment')->hasAttachments()
            && !isset($post['shipping_confirmation'])) {
            throw new Mage_Core_Exception(Mage::helper('rma')->__('Please, post not empty message'));
        }
        if (trim($comment) != '' || Mage::helper('mstcore/attachment')->hasAttachments()) {
            $rma->addComment($comment, false, $rma->getCustomer(), false, false, true);
        }
    }
}