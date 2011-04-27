<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Adminhtml_FilterController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
	    $this->loadLayout(); 
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addBreadcrumb($this->__('Filters'), $this->__('Filters')); 
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_filter')); 	    
 	    $this->renderLayout();
    }

    // load filters and their options
    // todo - syncronize options
	public function newAction() 
	{
	    try {
            Mage::getResourceModel('amshopby/filter')->createFilters();
            $msg = Mage::helper('amshopby')->__('Filters and their options have been loaded');
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
	    }
	    catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
		}
        $this->_redirect('*/*/');
	}
	
	// edit filters (uses tabs)
    public function editAction() {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amshopby/filter')->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Filter does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		
		Mage::register('amshopby_filter', $model);

		$this->loadLayout();
		
		$this->_setActiveMenu('catalog/amshopby');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_filter_edit'))
             ->_addLeft($this->getLayout()->createBlock('amshopby/adminhtml_filter_edit_tabs'));
        
		$this->renderLayout();
	}

	public function saveAction() 
	{
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('amshopby/filter');
	    $data = $this->getRequest()->getPost();
		if ($data) {
			$model->setData($data)->setId($id);
            
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('amshopby')->__('Filter properties have been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);

                $this->_redirect('*/*/');
               
				
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }	
            		    
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find a filter to save'));
        $this->_redirect('*/*/');
	} 
		
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('filter_id');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Please select filter(s)'));
        } 
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('amshopby/filter')->load($id);
                    $model->delete();
                    // todo delete values or add a foreign key
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    //for ajax
	public function valuesAction() 
	{
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getModel('amshopby/filter');

        if ($id) {
            $model->load($id);
        }

        Mage::register('amshopby_filter', $model);

		$this->getResponse()->setBody($this->getLayout()
		  ->createBlock('amshopby/adminhtml_filter_edit_tab_values')->toHtml());
	}    
}