<?php

class Dealer4dealer_Exactonline_Adminhtml_ExactonlineSynchronizeController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$this->loadLayout();

        try {
            $connector = Mage::getModel('exactonline/main');
            $connector->runUpdate();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('exactonline')->__('Synchronization complete.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exactonline')->__($e->getMessage()));
        }

        $this->_redirect('*/adminhtml_exactonlinesetting/index');
	}

	public function readLog($path){
		$block = $this->getLayout()->createBlock('exactonline/adminhtml_update');

		if(file_exists($path)) {
			$this->_addContent($this->getLayout()->createBlock('core/text')->setText($path))->_addLeft($block);
		}
	}

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dealer4dealer_menu');
    }
}