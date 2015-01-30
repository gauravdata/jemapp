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

class Nostress_Nscexport_Adminhtml_Nscexport_Profiles_GridController extends Mage_Adminhtml_Controller_Action
{
	const LINK_BATCH_SIZE = 10;

	protected $_helper;

	protected function _initProfile() {
		$this->_title($this->__('Catalog'))
			->_title($this->__('Koongo Connector'))
			->_title($this->__('Export Profiles'));

		$profileId = (int)$this->getRequest()->getParam('id', false);
		$profile = Mage::getModel('nscexport/profile');

		if ($profileId) {
			$profile->load($profileId);
		}

		if ($activeTabId = (string)$this->getRequest()->getParam('active_tab_id')) {
			Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
		}

		Mage::register('profile', $profile);
		Mage::register('current_profile', $profile);
		return $profile;
	}

	public function preDispatch() {
		parent::preDispatch();
	}
	
	protected function _initAction() {
		$vh = Mage::helper('nscexport/version');
		if($vh->isLicenseKeyT())
		{
			$this->_getSession()->addNotice($this->helper()->__('You are using the 30 days Trial version of Koongo Connector'));
			$this->_getSession()->addNotice($this->helper()->__('Your Trial period expires on %s and we encourage you to buy <a href="%s" target="blank" >Full version</a>.',$vh->gLD(),Nostress_Nscexport_Helper_Version::NEW_LICENSE_URL));
		}
		
		$this->_checkFlatEnabled();		
		
		if($this->helper()->isDebugMode())
		{
			$this->_getSession()->addNotice($this->helper()->__('Debug mode is On. See the results of <a href="%s" target="blank" >setup and configuration check</a>. You can turn off Debug mode in <a href="%s" >Configuration</a>.'
						,Mage::helper("adminhtml")->getUrl('koongo/check')
						,Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/koongo_config')));
		}

		try
		{
			Mage::helper('nscexport/data_loader')->checkFlatCatalogs();
		}
		catch(Exception $e)
		{
			$error = $this->helper()->processErrorMessage($e->getMessage());
			$this->_getSession()->addNotice($this->helper()->__('Koongo Connector requires data reindex. Please <a href="%s" target="_blank">reindex product and category data.</a>',Mage::helper("adminhtml")->getUrl("adminhtml/process/list/")));
			$this->_getSession()->addNotice($error['message']);
			$this->addHelpLink($error['link'],"notice");
		}

		if ($this->getRequest()->getQuery('ajax')) {
			$this->_forward('grid');
			return;
		}
		$this->loadLayout();

		/**
		* Set active menu item
		*/
		$this->_setActiveMenu('koongoconnector');

		/**
		* Add breadcrumb item
		*/
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Koongo Connector'), Mage::helper('adminhtml')->__('Koongo Connector'));

		return $this;
	}

	/**
	* Generate export for search engines
	*/
	public function generateAction()
	{
		// init and load export model
		$profileId = $this->getRequest()->getParam('export_id');
		// if nscexport record exists
		if ($profileId > 0) {
			try
			{
				$profile = Mage::getModel('nscexport/profile')->load($profileId);
				$this->helper()->runProfile($profile, false);
				$this->_getSession()->addSuccess($this->getSuccessRunMessage($profileId).$this->helper()->__($profile->getMessage()));
			}
			catch (Exception $e)
			{
				$this->_getSession()->addException($e, $this->helper()->__('Unable to generate an export').". ".$profile->getMessage());
			}
		}
		else {
			$this->_getSession()->addError($this->helper()->__('Unable to find an export to generate'));
		}

		// go to grid
		$this->_redirect('*/*/');
	}

	/**
     * Reload engines taxonomy table
     */
    public function reloadtaxonomyAction($redirect = true)
    {
    	$message = $this->helper()->__("Taxonomy reload failed: ");
    	$vh = Mage::helper('nscexport/version');
    	if(!$vh->isLicenseValid(true))
    	{
    		$this->_getSession()->addError($message.$vh->getLicenseInvalidMessage());
    		$this->_redirect('*/*/');
    		return false;
    	}
    	try
    	{
    		Mage::getModel('nscexport/entity_attribute_taxonomy')->prepareAttributes();
        	$message = Mage::getModel('nscexport/taxonomy')->reloadTaxonomy();
            $this->_getSession()->addSuccess($this->helper()->__("Taxonomy successfully reloaded."));
        }
        catch (Exception  $e)
        {
        	$this->_getSession()->addError($message. $e->getMessage());
        	$this->addTroubleshootingLink();
        	if(!$redirect)
        		return false;
        }
		$this->renderMessage($message);
        
		// go to grid
		if($redirect)
			$this->_redirect('*/*/');
		return true;
    }

    /**
     * Reload engines taxonomy table
     */
    public function reloadfeedconfigAction($redirect = true)
    {
    	try
    	{
    	    $client = Mage::helper('nscexport/data_client');
    	    $client->updatePlugins();
    	    $client->updateLicense();
    		$links = $client->updateFeeds();
    		$this->_getSession()->addSuccess($this->__('Following feeds have been updated:'));
    		sort($links);
    		$linkBatch = array();
    		foreach($links as $link)
    		{
    			$linkBatch[] = $link;
    			if(count($linkBatch) >= self::LINK_BATCH_SIZE)
    			{
    				$this->addLinksToSessionSuccess($linkBatch);
    				$linkBatch = array();
    			}
    		}

    		if(!empty($linkBatch))
    			$this->addLinksToSessionSuccess($linkBatch);
        }
        catch (Exception  $e)
        {
        	$message = $this->helper()->__("Feeds specification load failed: ");
        	$this->_getSession()->addError($message. $e->getMessage());
        	if(!$redirect)
        		return false;
        }
		//$this->renderMessage($message);
        if($redirect)
        	$this->_redirect('*/*/');
        return true;
    }
    
    public function reloadfeedtaxonomiesAction()
    {
    	if($this->reloadfeedconfigAction(false))
    		$this->reloadtaxonomyAction(false);
    	$this->_redirect('*/*/');
    }

    protected function addLinksToSessionSuccess($linkBatch)
    {
    	$this->_getSession()->addSuccess("᛫".implode("&nbsp;&nbsp;&nbsp;᛫",$linkBatch));
    }

    protected function renderMessage($message)
    {
    	if(isset($message[true]))
    	{
    		$this->addSessionMessage($message[true]);
    	}

    	if(isset($message[false]))
    	{
    		$this->addSessionMessage($message[false],true);
    	}
    }

	protected function addSessionMessage($message,$error = false) {
		if (!is_array($message))
			$message = array($message);

		foreach($message as $item) {
			if ($error)
				$this->_getSession()->addError($item);
			else
				$this->_getSession()->addSuccess($item);
		}
	}

	public function gridAction() {
		$this->getResponse()->setBody($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_grid')->toHtml());
	}

	public function indexAction() {
		Mage::helper('nscexport/version')->checkLicense();
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		Mage::helper('nscexport/version')->checkLicense();
		$params['_current'] = true;
		$id = $this->getRequest()->getParam('id');
		$storeId = (int)$this->getRequest()->getParam('store');
		if (isset($id))
		{
			$profile = Mage::getModel('nscexport/profile')->load($id);
			$storeId = $profile->getStoreId();
		}
		else
		{
			$feedId = $this->getRequest()->getParam('file');
			$profile = Mage::getModel('nscexport/profile');
			$profile->setFeed($feedId);
			$profile->setStoreId($storeId);
		}

		try
		{
			Mage::helper('nscexport/data_loader')->checkFlatCatalog($storeId);
		}
		catch(Exception $e)
		{
			$error = $this->helper()->processErrorMessage($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($this->helper()->__("Can't edit profile.")." ".$error['message']);
			$this->addHelpLink($error['link']);
			$this->_redirect('*/*/');
			return;
		}

		$this->_title($this->helper()->__('Koongo Connector')." - ".($id ? Mage::helper('nscexport')->__('Edit Profile')." - ".$profile->getName() : Mage::helper('nscexport')->__('New Profile')));

		if (isset($profile)) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$profile->setData($data);
			}

			Mage::register('nscexport_profile', $profile);
			$this->loadLayout();
			$this->_setActiveMenu('koongoconnector/nscexportprofiles');
			$this->getLayout()->getBlock('head')
			    ->setCanLoadExtJs(true)
			    ->setCanLoadRulesJs(true);

			$this->renderLayout();
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError($this->helper()->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		Mage::helper('nscexport/version')->checkLicense();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_new'));
		$this->renderLayout();
	}


	public function saveAction() {
	    
		Mage::helper('nscexport/version')->validateLicenceBackend();

		if (!$profile = $this->_initProfile()) {
			return;
		}
		if ($data = $this->getRequest()->getPost()) {
			try {
				$profile->processData($data, $this->getRequest()->getParam('id'));
				Mage::getSingleton('adminhtml/session')->addSuccess($this->helper()->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $profile->getId()));
					return;
				}

			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->addTroubleshootingLink();
			}
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError($this->helper()->__('Unable to find item to save'));
		}
		$this->_redirect('*/*/');
		return;
	}

	public function deleteAction()
	{
	    $id = $this->getRequest()->getParam('id');
		if($id > 0 )
		{
			try
			{
			    $profile = Mage::getModel('nscexport/profile')->load($id);
			    $profile->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function testFtpConnectionAction() {
	    
	    Mage::helper('nscexport/version')->checkLicense();
	    
	    if( $this->_request->isPost() && ($postData = $this->_request->getPost()) !== false && isset( $postData['upload']))
	    {
	        $config = $postData['upload'];
	        $profile = Mage::getModel('nscexport/profile');
	        $message = $profile->checkFtpConnection( $config);
	    } elseif(($id = $this->_request->getParam( 'id')) > 0)
	    {
            $profile = Mage::getModel('nscexport/profile');
            $profile->load( $id);
            $config = $profile->getUploadParams();
            $message = $profile->checkFtpConnection( $config);
	    } else {
	        $message = $this->helper()->__( "Wrong data format!");
	    }
	    $this->_response->setBody( Zend_Json::encode($message));
	}
	
	public function uploadFeedAction() {

	    Mage::helper('nscexport/version')->checkLicense();
	   	   
	    $id = $this->getRequest()->getParam('id');
	    if (isset($id))
	    {
	        $profile = Mage::getModel('nscexport/profile')->load($id);
	        if( !$profile->getId()) {
	            $message = $this->helper()->__( "Item does not exist");
	        } else {
	            try {
                    $profile->uploadFeed();
                    $pMessage = str_replace("Upload: OK", '', $profile->getMessage());
                    $profile->setMessage( $pMessage." ".$this->__('Upload:')." OK");
                    $profile->setStatus( Nostress_Nscexport_Model_Profile::STATUS_FINISHED);
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->helper()->__('Feed was successfuly uploaded via FTP!'));
	            } catch( Exception $e) {
	                $profile->setMessageStatusError( $this->__('Upload:')." ".$e->getMessage(),
	                        Nostress_Nscexport_Model_Profile::STATUS_ERROR);
	                Mage::getSingleton('adminhtml/session')->addError( $e->getMessage());
	            }
	            $this->_redirect('*/*/');
	            return;
	        }
	    } else {
	        $message = $this->helper()->__( "Item does not exist");
	    }
	    Mage::getSingleton('adminhtml/session')->addError( $message);
	    $this->_redirect('*/*/');
	}

	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
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
		die; // Maybe die();
	}

	public function categoriesJsonAction() {
		if ($this->getRequest()->getParam('expand_all')) {
			Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
		}
		else {
			Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
		}

		$category = $this->_initCategory();
		$this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/catalog_category_tree')->getTreeJson($category));
	}

	protected function _initCategory() {
		$categoryId = (int)$this->getRequest()->getParam('id');

		$category = Mage::getModel('catalog/category');
		$category->load($categoryId);
		return $category;
	}

	public function massDeleteAction() {
		$profileIds = $this->getRequest()->getParam('profile');
		if (!is_array($profileIds)) {
			$this->_getSession()->addError($this->__('Please select profile(s)'));
		}
		else {
			try {
				foreach ($profileIds as $profileId) {
					$profile = Mage::getModel('nscexport/profile')->load($profileId);
					$profile->delete();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully deleted', count($profileIds)));
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massGenerateAction() {
		$profileIds = $this->getRequest()->getParam('profile');
		if (!is_array($profileIds)) {
			$this->_getSession()->addError($this->__('Please select profile(s)'));
		}
		else {
			$addLink = false;
			try
			{
				$profiles = Mage::getModel('nscexport/profile')->getProfilesByIds($profileIds);
				$this->helper()->runProfiles($profiles, false);
				$successCounter = 0;

				foreach($profiles as $profile)
				{
					$message = $profile->getMessage();
					$status = $profile->getStatus();
					if($status != Nostress_Nscexport_Model_Unit_Control::STATUS_ERROR)
					{
						$successCounter++;
						$this->_getSession()->addSuccess($this->getSuccessRunMessage($profile->getId()).$message);
					}
					else
					{
						$this->_getSession()->addError($message);
					}
				}

				$this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully generated', $successCounter));
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function productGridAction()
	{
		$categoryId = (int) $this->getRequest()->getParam('id',0);
		Mage::register('category',Mage::getModel('catalog/category')->load($categoryId));
		Mage::register('nscexport_storeid', (int) $this->getRequest()->getParam('store', 0));
		$block = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_grid', 'nscexport_catalog_category_products')
			->setData(
				array(
					'category' => $categoryId,
					'store' => ((int) $this->getRequest()->getParam('store', 0))
				));
		$block->manualInit();
		$this->getResponse()->setBody($block->toHtml().$block->getChosenProductsHtml());
	}

	protected function getSuccessRunMessage($id)
	{
		return $this->helper()->__("Export profile # %s has been successfully generated.",$id)." ";
	}
	
	protected function addTroubleshootingLink($addLink = true)
	{
        if($addLink)
            $this->_getSession()->addError($this->helper()->__('Please navigate to <a href="%s" target="_blank">Troubleshooting</a> to find the solution for this problem.', Mage::helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Data::HELP_TROUBLE)));

	    if(!Mage::helper('nscexport/version')->isLatestVersionInstalled())
		{
			$helpLink = Mage::helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Version::MODULE_UPDATE_LINK);
			$versionNumber = Mage::helper('nscexport/version')->getLatestModuleVersion();
			$this->_getSession()->addError($this->helper()->__('Please <a href="%s" target="_blank">install latest Koongo Connecotor version %s</a> to fix this problem.', $helpLink,$versionNumber));
		}
	}
	
	protected function addHelpLink($link,$type = "error")
	{
		if(empty($link))
			return;
		$message = $this->helper()->__('The solution for this problem is described in <a href="%s" target="_blank">Koongo Docs</a>.', $link);
	
		switch($type)
		{
			case "error":
				$this->_getSession()->addError($message);
				break;
			case "notice":
				$this->_getSession()->addNotice($message);
				break;
		}
	}

	protected function _checkFlatEnabled()
	{
		$checkHelper = Mage::helper('nscexport/data_check');
		
		//product flat
		$productResult = $checkHelper->testFlat();
		//category
		$categoryResult = $checkHelper->testFlat('category');
			
		if($categoryResult == 0 || $productResult == 0)
		{
			$actionLink = Mage::helper('adminhtml')->getUrl('adminhtml/nscexport_action/enableFlat');
			$helpLink = $this->helper()->getHelpUrl(Nostress_Nscexport_Helper_Data::HELP_FLAT_CATALOG);
			$this->_getSession()->addNotice($this->helper()->__("Flat Catalog Category and Flat Catalog Product usage is required. <a href=\"%s\">Click to enable Flat Catalog</a>.",$actionLink));
			$this->_getSession()->addNotice($this->helper()->__("More information you may find in <a target=\"_blank\" href=\"%s\">Koongo Docs</a>.",$helpLink));
		}
	}
	
	protected function helper() {
		if (!isset($this->_helper))
			$this->_helper = Mage::helper('nscexport/data_profile');
		return $this->_helper;
	}
}