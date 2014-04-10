<?php
/**
* Magento Module developed by NoStress Commerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@nostresscommerce.cz so we can send you a copy immediately.
*
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
* Hlavni kontroler pro exportni intarface
*
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Adminhtml_Nscexport_ActivationController extends Mage_Adminhtml_Controller_Action
{
	protected $_helper;
	
	protected function _initAction()
	{
		$this->loadLayout();
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_initAction();
		$block = $this->getLayout()->getBlock('nscexport_activation');
		$form = $block->getChild("form");
		$contact = $block->getChild("contactForm");
		if ($block) 
		{
			$params = $this->getRequest()->getParams();
			$block->setData($params);
			$form->setData($params);
			$contact->setData($params);
		}
		$this->renderLayout();
	}
	
	public function activateAction()
	{
		try
		{
			$params = $this->getRequest()->getParams();
			if(!empty($params["collection"]))
				$params["collection"] = array($params["collection"]);			
			$result = Mage::helper('nscexport/data_client')->createLicenseKey($params);
			
			$this->_getSession()->addSuccess($this->__('Koongo Connector has been activated with license key %s .',$result['key']));
			$this->_getSession()->addSuccess($this->__('Feed collection %s has been assigned to the license key.',implode(", ", $result['collection'])));			
		}
		catch (Exception  $e)
		{
			$message = $this->helper()->__("Module activation process failed. Error: ");
			$this->_getSession()->addError($message. $e->getMessage());		
			$code = "";
			$urlParam = array();
			if(!empty($params["code"]))	
				$urlParam = array("code" => $params["code"]);
			$this->_redirect('*/*/',$urlParam);
			return;
		}
		
		if($this->helper()->isDebugMode())
			$this->_redirect('adminhtml/nscexport_profiles_grid/index');
		else
			$this->_redirect('adminhtml/nscexport_profiles_grid/reloadfeedtaxonomies');
	}
		
	public function postAction()
	{
		$post = $this->getRequest()->getPost();
		
		if ( $post ) {
			if(!empty($post['form_key']))
				unset($post['form_key']);
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			try 
			{				
				$error = false;
				
				if (!Zend_Validate::is(trim($post['body']) , 'NotEmpty')) {
					$error = true;
				}
				
				if (!Zend_Validate::is(trim($post['from_email']), 'EmailAddress')) {
					$error = true;
				}
				
				if ($error) {
					throw new Exception();
				}				
				
				$toEmail = $this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_SUPPORT_EMAIL);
				
				$mail = Mage::getModel('core/email');
				$mail->setData($post);				
				$mail->setToEmail($toEmail);
				$mail->setType('text');// You can use 'html' or 'text'				
				$mail->send();				
	
				$translate->setTranslateInline(true);
	
				$this->_getSession()->addSuccess(Mage::helper('nscexport')->__('Your inquiry has been submitted and we should respond within 24 hours. Thank you for contacting Koongo Support.'));
			} 
			catch (Exception $e) 
			{				
				$this->_getSession()->addError(Mage::helper('nscexport')->__('Unable to submit your request. Please, contact Koongo support desk via email address %s',$this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_SUPPORT_EMAIL)));
				$this->_getSession()->addError(Mage::helper('nscexport')->__('Error: ').$e->getMessage());
			}
	
		}
		$code = $this->getRequest()->getParam('code','KOONGO_FREE_TRIAL_30D');		
		$this->_redirect('*/*/',array("code" => $code));
	}
	
	protected function helper() 
	{
		if (!isset($this->_helper))
			$this->_helper = Mage::helper('nscexport');
		return $this->_helper;
	}
}