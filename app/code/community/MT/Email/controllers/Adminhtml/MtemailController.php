<?php


class MT_Email_Adminhtml_MtemailController
    extends Mage_Adminhtml_Controller_Action
{

    public function installAjaxAction()
    {
        $success = 0;
        $error = '';
        $params = $this->getRequest()->getParams();
        try {
            Mage::getModel('mtemail/adminhtml_action')->install($params);
            $success = Mage::helper('mtemail/adminhtml')->__('Email templates have been created successful.');
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function deleteAjaxAction()
    {
        $success = 0;
        $error = '';
        $params = $this->getRequest()->getParams();

        try {
            Mage::getModel('mtemail/adminhtml_action')->delete($params);
            $success = Mage::helper('mtemail/adminhtml')->__('Email templates have been deleted successful.');
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function replaceAjaxAction()
    {
        $success = 0;
        $error = '';
        $params = $this->getRequest()->getParams();

        try {
            Mage::getModel('mtemail/adminhtml_action')->replace($params);
            $success = Mage::helper('mtemail/adminhtml')->__('System config have been updated successful.');
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }

    public function revertAjaxAction()
    {
        $success = 0;
        $error = '';
        $params = $this->getRequest()->getParams();

        try {
            Mage::getModel('mtemail/adminhtml_action')->revertConfig($params);
            $success = Mage::helper('mtemail/adminhtml')->__('System config have been reverted successful.');
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'error' => $error,
            'success' => $success,
        )));
    }
}
