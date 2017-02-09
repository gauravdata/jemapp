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



class Mirasvit_Rma_Helper_Ruleevent extends Mage_Core_Helper_Abstract
{
    protected $_sentEmails = array();
    protected $_processedEvents = array();

    public function getConfig()
    {
        return Mage::getSingleton('rma/config');
    }

    /**
     * @param string                 $eventType
     * @param Mirasvit_Rma_Model_Rma $rma
     */
    public function newEvent($eventType, $rma)
    {
        $key = $eventType.$rma->getId();
        if (isset($this->_processedEvents[$key])) {
            return;
        } else {
            $this->_processedEvents[$key] = true;
        }

        $this->_sentEmails = array();
        $collection = Mage::getModel('rma/rule')->getCollection()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('event', $eventType);
        $collection->getSelect()->order('sort_order');

        /** @var Mirasvit_Rma_Model_Rule $rule */
        foreach ($collection as $rule) {
            $rule->afterLoad();
            // var_dump($rule->validate($rma));die;
            if (!$rule->validate($rma)) {
                continue;
            }
            if ($this->processRule($rule, $rma) && $rule->getIsStopProcessing()) {
                break;
            }
        }
    }

    /**
     * @param Mirasvit_Rma_Model_Rule $rule
     * @param Mirasvit_Rma_Model_Rma  $rma
     *
     * @return bool
     */
    protected function processRule($rule, $rma)
    {
        $processed = false;
        /* set attributes **/
        if ($rule->getStatusId()) {
            $rma->setStatusId($rule->getStatusId());
            $processed = true;
        }
        if ($rule->getUserId()) {
            $rma->setUserId($rule->getUserId());
            $processed = true;
        }

        if ($rule->getIsArchived() == Mirasvit_Rma_Model_Config::IS_ARCHIVE_TO_ARCHIVE) {
            $rma->setIsArchived(1);
        } elseif ($rule->getIsArchived() == Mirasvit_Rma_Model_Config::IS_ARCHIVE_FROM_ARCHIVE) {
            $rma->setIsArchived(0);
        }

//        if ($tags = $rule->getAddTags()) {
//            Mage::helper('rma/tag')->addTags($rma, $tags);
//        }
//        if ($tags = $rule->getRemoveTags()) {
//            Mage::helper('rma/tag')->removeTags($rma, $tags);
//        }
        if (!$rma->getId()) {
            Mage::register('rma_created', 'yes');
        }

        $rma->save();

        /* send notifications **/
        if ($rule->getIsSendUser()) {
            if ($user = $rma->getUser()) {
                $this->_sendEventNotification($user->getEmail(), $user->getName(), $rule, $rma);
                $processed = true;
            }
        }
        if ($rule->getIsSendCustomer()) {
            if ($customer = $rma->getCustomer()) {
                $this->_sendEventNotification($customer->getEmail(), $customer->getName(), $rule, $rma);
                $processed = true;
            }
        }
        if ($otherEmail = $rule->getOtherEmail()) {
            $this->_sendEventNotification($otherEmail, '', $rule, $rma);
            $processed = true;
        }

        if (Mage::registry('rma_created')) {
            Mage::unregister('rma_created');
        }

        return $processed;
    }

    /**
     * @param string                  $email
     * @param string                  $name
     * @param Mirasvit_Rma_Model_Rule $rule
     * @param Mirasvit_Rma_Model_Rma  $rma
     */
    protected function _sendEventNotification($email, $name, $rule, $rma)
    {
        if (!is_array($this->_sentEmails) || !in_array($email, $this->_sentEmails)) {
            Mage::helper('rma/mail')->sendNotificationRule($email, $name, $rule, $rma);
            $this->_sentEmails[] = $email;
        }
    }
}
