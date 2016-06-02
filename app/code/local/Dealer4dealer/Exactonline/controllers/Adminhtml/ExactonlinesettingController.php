<?php
class Dealer4dealer_Exactonline_Adminhtml_ExactonlineSettingController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('dealer4dealer_menu/exactonline_settings')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Settings Manager'), Mage::helper('adminhtml')->__('Setting Manager'));
        return $this;
    }

	public function indexAction()
	{
		$this->_initAction();

		$this->_addContent($this->getLayout()->createBlock('exactonline/adminhtml_exactonline'));
		$this->_addLeft($this->getLayout()->createBlock('exactonline/adminhtml_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
		$Id     = $this->getRequest()->getParam('id');
		$Model  =	Mage::getModel('exactonline/setting')->load($Id);

		if($Model->getFieldType() == 3) {
			$Model->setValue('');
		}

		if($Model->getId() || $Id ==	0) {
			Mage::register('exactonline_data', $Model);
			$this->loadLayout();
			$this->_setActiveMenu('dealer4dealer_menu/exactonline_settings');
			$this->_addBreadcrumb(Mage::helper('exactonline')->__('Settings'), Mage::helper('exactonline')->__('Settings'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Settings'), Mage::helper('adminhtml')->__('Settings'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('exactonline/adminhtml_exactonline_edit'))
				->_addLeft($this->getLayout()->createBlock('exactonline/adminhtml_exactonline_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This item does not exist'));
			$this->_redirect('*/*/');
		}
    }

    public function newAction(){
		$this->_forward('edit');
    }

	public function saveAction()
    {
		if ( $this->getRequest()->getPost() ) {
			try {
				$postData = $this->getRequest()->getPost();
				$Model = Mage::getModel('exactonline/setting');

				$Model->load($this->getRequest()->getParam('id',null));

				if((bool)$Model->getIsEditableKey() || is_null($this->getRequest()->getParam('id',null))) {
					$Model->setName($postData['name']);
				}

				if($Model->getFieldType() == 3) {
					$value = $postData['value'];
					if($value != '') {
						$encrypted = Mage::helper('core')->encrypt(base64_encode($value));
						$Model->setValue($encrypted);
					}
				}else {
					$Model->setValue($postData['value']);
				}

				$Model->setCategoryId($postData['category_id']);
				$Model->setLabel($postData['label']);
				$Model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Setting was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setData(false);
				$this->_redirect('*/*/index',array('category'=>$Model->getCategoryId()));
				return;

			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->setData($this->getRequest()->getPost());
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Error while saving.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
    }

    public function deleteAction()
    {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$Model = Mage::getModel('exactonline/setting');
				$Model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Setting was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
    }

	public function debugAction()
    {
        $this->loadLayout();

        // Get the current debug Id
        $setting = Mage::getModel('exactonline/setting')->load('debug_log_id', 'name');
        $debugId = (int)$setting->getValue();

        // Load the related order log
        $log = Mage::getModel('exactonline/log_order')->load($debugId, 'order_id');

        $cookiePath = Mage::getBaseDir('var') . DS . 'log' . DS . 'd4d' . DS . 'magentoExactOnlineCookie.txt';

        $block = $this->getLayout()
            ->createBlock('core/text', 'exactonline_debug_info')
            ->setText($cookiePath .' <br /><br />' . htmlentities($log->getRawXmlResponse()));

        $this->_addContent($block);

        $this->renderLayout();
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('dealer4dealer_menu');
	}
}