<?php
/**
 * ArtsOnIT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://www.mageext.com/respository/docs/License-SourceCode.pdf
 *
 * @category   ArtsOnIT
 * @package    ArtsOnIT_Autologin
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 ArtsonIT di Calore (http://www.mageext.com)
 * @license    http://www.mageext.com/respository/docs/License-SourceCode.pdf
 */
class ArtsOnIT_Autologin_Adminhtml_AutologinController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('autologin/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
	public function sendAction() {

	
	$templ  = Mage::getModel('core/email_template')->load(2);									

				$templ->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
				$templ->setSenderName('MageExt');
				
				
				 if (!$templ->send( 'l.calore@gmail.com' , 'l.calore@gmail.com', 
									  array('customer' => Mage::getModel('customer/customer')->load(4943)
								) ))
							{
								echo 'ok';
							}
							 
	}
 		
	public function indexAction() {

	
		$this->_initAction()
		 ->_addContent($this->getLayout()->createBlock('autologin/adminhtml_autologin'))
			->renderLayout();
	}
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('autologin/adminhtml_autologin_grid')->toHtml()
        );
    }
    public function massChangehashAction() {
        $customerIds = $this->getRequest()->getParam('customer');
        if(!is_array($customerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                   Mage::helper('autologin')->generateAutologin($customerId, false);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('autologin')->__(
                        'Total of %d customer(s) were successfully updated', count($customerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massEnabledAction()
    {
        $customerIds = $this->getRequest()->getParam('customer');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select customer(s)'));
        } else {
            try {
            	foreach ($customerIds as $customerId) {
            	   $customer=  Mage::getModel('customer/customer')->load($customerId);
                   $customer->setData('autologin_is_active', (bool)$this->getRequest()->getParam('status'));
	    		   Mage::getResourceSingleton('customer/customer')->saveAttribute($customer, 'autologin_is_active');
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('autologin')->__(
                        'Total of %d customer(s) were successfully updated', count($customerIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'autologin.csv';
        $content    = $this->getLayout()->createBlock('autologin/adminhtml_autologin_grid')
        				->setIsWeb(false) 
            			->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'autologin.xml';
        $content    = $this->getLayout()->createBlock('autologin/adminhtml_autologin_grid')
        				->setIsWeb(false) 
           				->getXml();
 
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}