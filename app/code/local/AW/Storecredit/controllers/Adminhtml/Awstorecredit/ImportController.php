<?php
class AW_Storecredit_Adminhtml_Awstorecredit_ImportController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/aw_storecredit/import');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/transactions');

        $this
            ->_title($this->__('Sales'))
            ->_title($this->__('Store Credit'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();
        $this->_title($this->__('Import'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $actionIfCustomerExist = Mage::app()->getRequest()->getPost('action_if_customer_exist', 1);
        $import = Mage::getModel('aw_storecredit/import');

        try {
            $import->uploadFile();

            $result = $import->importFromFile($actionIfCustomerExist);
            if($result['status']){
                Mage::getSingleton('adminhtml/session')->addSuccess($result['message']);
            }else{
                Mage::getSingleton('adminhtml/session')->addError($result['message']);
            }
        }catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
}