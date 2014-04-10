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
 * @package    AW_Customsmtp
 * @version    1.0.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Customsmtp_Adminhtml_MailsController extends Mage_Adminhtml_Controller_Action
{
    protected function _addTitle($title)
    {
        if (method_exists($this, '_title')) {
            $this->_title($title);
        }
        return $this;
    }

    public function indexAction()
    {
        $this->_addTitle($this->__('Custom SMTP'))->_title($this->__('Emails List'));
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->renderLayout();
    }

    public function viewAction()
    {
        $post = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
        Mage::register('awcsmtp_current_mail', $post);

        $this->_addTitle($this->__('Custom SMTP'))->_title($this->__('View Email'));
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->renderLayout();
    }

    public function deleteAction()
    {
        try {
            $entry = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
            if ($entry->getId()) {
                $entry->delete();
            } else {
                throw new Exception($this->__('Can\'t delete mail that doesn\'t exist'));
            }
            Mage::getSingleton('adminhtml/session')->addSuccess("Mail successfully deleted");
            $this->_redirect('*/index/');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    public function massDeleteAction()
    {
        $tickets = $this->getRequest()->getParam('mails');
        if (!is_array($tickets)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($tickets as $id) {
                    $ticket = Mage::getModel('customsmtp/mail')->load($id);
                    $ticket->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($tickets)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function testconnectionAction()
    {
        $request = $this->getRequest();
        $config = array(
            'port'     => $request->getParam('port'),
            'auth'     => $request->getParam('auth'),
            'username' => $request->getParam('user'),
            'password' => $request->getParam('pass')
        );
        if ($_secure = $request->getParam('secure')) {
            $config['ssl'] = $_secure;
        }

        $_canConnect = false;
        $_msg = '';

        try {
            $transport = new AW_Customsmtp_Model_Mail_Transport_Smtp($request->getParam('host'), $config);
            $_canConnect = $transport->canConnect();
        } catch (Exception $e) {
            $_canConnect = false;
            $_msg = $e->getMessage();
        }

        $this->getResponse()->setBody(
            Zend_Json::encode(
                array(
                     's'   => $_canConnect,
                     'msg' => $_msg,
                )
            )
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/mails/mails');
    }
}