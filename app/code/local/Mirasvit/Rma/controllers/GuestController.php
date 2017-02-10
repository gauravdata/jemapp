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


require_once Mage::getModuleDir('controllers', 'Mirasvit_Rma').DS.'AbstractRmaController.php';

/**
 * Public form for enter to RMA as guest.
 *
 * Class Mirasvit_Rma_Rma_GuestController
 */
class Mirasvit_Rma_GuestController extends Mirasvit_Rma_AbstractRmaController
{
    /**
     * Post action. Checks for correct email/order
     * @return void
     */
    public function guestAction()
    {
        $session = $this->_getSession();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId()) {
            $this->_redirectUrl(Mage::helper('rma/url')->getNewRmaUrl());
            return;
        }
        try {
            $order = $this->_initOrder();
            if ($order) {
                // Check for return eligibility
                if (!Mage::helper('rma')->isReturnAllowed($order)) {
                    $daysLeft = Mage::helper('rma/order')->getOrderAvailableDays($order->getId());
                    if ($daysLeft < 0) {
                        $errMessage = Mage::helper('rma')->__(
                            'This order were placed more than %s days ago. Please, contact customer service.',
                            Mage::helper('rma')->getReturnPeriod());
                    } else {
                        $errMessage = Mage::helper('rma')->__(
                            'This order is fully processed, returns unavailable.');
                    }
                    throw new Mage_Core_Exception($errMessage);
                }
                $this->_getSession()->setRmaGuestOrderId($order->getId());
                $this->_getSession()->setRmaGuestEmail($order->getCustomerEmail());
                $this->_redirectUrl(Mage::helper('rma/url')->getGuestRmaListUrl());

                return;
            } elseif (Mage::app()->getRequest()->getParam('order_increment_id')) {
                $store = Mage::app()->getStore();
                if (Mage::getSingleton('rma/config')->getPolicyAllowGuestOfflineRMA($store)) {
                    $this->_getSession()->setRmaGuestEmail(Mage::app()->getRequest()->getParam('email'));
                    $this->_redirectUrl(Mage::helper('rma/url')->getGuestOfflineRmaUrl());
                } else {
                    throw new Mage_Core_Exception(Mage::helper('rma')->__('Wrong Order #, Email or Last Name'));
                }
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function offlineAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * @param Mirasvit_Rma_Model_Rma $rma
     *
     * @return string
     */
    protected function getRmaViewUrl($rma)
    {
        return Mage::helper('rma/url')->getGuestRmaViewUrl($rma);
    }

    /**
     * @return string
     */
    protected function getRmaListUrl()
    {
        return Mage::helper('rma/url')->getGuestRmaListUrl();
    }

    /**
     * @return false|Mage_Sales_Model_Order
     */
    protected function _initOrder()
    {
        if (($orderId = Mage::app()->getRequest()->getParam('order_increment_id')) &&
            ($email = Mage::app()->getRequest()->getParam('email'))) {
            $orderId = trim($orderId);
            $orderId = str_replace('#', '', $orderId);
            $collection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('increment_id', $orderId);
            if ($collection->count()) {
                $order = $collection->getFirstItem();
                $email = trim(strtolower($email));
                if ($email != strtolower($order->getCustomerEmail())
                    && $email != strtolower($order->getCustomerLastname())) {
                    return false;
                }

                return $order;
            }
        }
    }
}
