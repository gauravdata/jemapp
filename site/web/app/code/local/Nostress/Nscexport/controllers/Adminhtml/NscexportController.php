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
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Hlavni kontroler pro exportni intarface
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/


require_once 'app/code/core/Mage/Adminhtml/Controller/Action.php';
/*
require_once 'app/code/local/Nostress/Nscexport/Helper/Data.php';
require_once 'app/Mage.php';
require_once 'app/code/core/Mage/Core/functions.php';
*/
class Nostress_Nscexport_Adminhtml_NscexportController extends Mage_Adminhtml_Controller_action
{

	public function preDispatch()
    {
    	Mage::helper('nscexport/version')->validateLicenceBackend();
        parent::preDispatch();
    }
    
	protected function _initAction() 
	{
		if ($this->getRequest()->getQuery('ajax')) 
		{
            $this->_forward('grid');
            return;
    	}
    	$this->loadLayout();   
		/**
   		* Set active menu item
    	*/
    	$this->_setActiveMenu('system/convert');
		/**
    	* Add breadcrumb item
    	*/
    	$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
    	//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Profiles'), Mage::helper('adminhtml')->__('Profiles'));
    	$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Export for search engine'), Mage::helper('adminhtml')->__('Export for search engine'));

		//$this->renderLayout();
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
        if($profileId > 0) 
        {
            try 
            {   
                Nostress_Nscexport_Helper_Data::generateProfile($profileId);
                $this->_getSession()->addSuccess(Mage::helper('nscexport')->__(Mage::getModel('nscexport/nscexport')->load($profileId,'export_id')->getMessage()));
            }
            catch (Mage_Core_Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) 
            {
                $this->_getSession()->addException($e, Mage::helper('nscexport')->__('Unable to generate an export').". ".$e->getMessage());
            }
        }
        else 
        {
            $this->_getSession()->addError(Mage::helper('nscexport')->__('Unable to find an nscexport to generate'));
        }

        // go to grid
        $this->_redirect('*/*/');
    }
	
	public function gridAction()
  	{
    	$this->getResponse()->setBody($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_grid')->toHtml());
  	}
	
 
	public function indexAction() 
	{
		$this->_initAction()->renderLayout();
	}

	public function editAction() 
	{		
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('nscexport/nscexport')->load($id);

		if ($model->getId() || $id == 0) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('nscexport_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('nscexport/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		
			$wizardBlock = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_wizard');  	  
  	  		
			$model->setSearchengine(strtolower($model->getSearchengine()));
			$wizardBlock->addData($model->getData());   	 
  	  		
			$this->_addContent($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit'))
				->_addContent($wizardBlock);
			$this->renderLayout();
		} 
		else 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nscexport')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
		
	}
 
	public function newAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);		
		$this->_addContent($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_new'))
				->_addContent($this->getLayout()->createBlock('nscexport/adminhtml_nscexport_new_settings'));
		$this->renderLayout();		
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) 
		{
			if(isset($data['nscexport_profile_name']))
				$data['name'] = $data['nscexport_profile_name'];
			$model = Mage::getModel('nscexport/nscexport');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try 
			{
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) 
				{
					$model->setCreatedTime(now());						
				} 														

				//add suffix to file
				$model->setFilename(Nostress_Nscexport_Helper_Data::addFileSuffix($model->getFilename(),$model->getSearchengine()));
				
				$tempPrefix = (string)Mage::getConfig()->getNode('default/nscexport/temp_file_prefix');
				if($model->getId() != '')
				{					
					//Get old export values 
					$oldModel = Mage::getModel('nscexport/nscexport')->load($model->getId(),'export_id');	
			    	
					$deleteFeed = false;
					$updateConfigFiealds = false;
					
					$oldCategoryProductIds = Mage::getModel('nscexport/categoryproducts')->getExportCategoryProducts($model->getId());
					
					//If search engine was changed => start times have to be recounted
        			if($oldModel->getSearchengine() != $model->getSearchengine())
        			{
        				$updateConfigFiealds = true;
        				$deleteFeed = true;					
        			}					
					//categories to export were changed
        			else if($oldCategoryProductIds != $model->getCategoryProductIds()	|| $oldModel->getCentrumcategory() != $model->getCentrumcategory()  )
        			{
        				$deleteFeed = true;						
        			}
        			else 
        			{
        				//rename xml files
						if($model->getFilename() != $oldModel->getFilename())  
						{
							$engineName = strtolower($model->getSearchengine());							
							Nostress_Nscexport_Helper_Data::renameSearchengineProfileFiles($engineName,$oldModel->getFilename(),$model->getFilename(),$tempPrefix);
							$model->setUrl(Nostress_Nscexport_Helper_Data::getXmlUrl($model->getFilename(),$engineName));
						}
        			}
        			if($deleteFeed)
        			{
        				//delete feed and temp feed files
						Nostress_Nscexport_Helper_Data::deleteSearchengineProfileFiles($oldModel->getSearchengine(),$oldModel->getFilename(),$tempPrefix);
					    $model->setUrl(Mage::helper('nscexport')->__('XML file doesnt exist'));
        			}
        			$model->save();
        			if($updateConfigFiealds)
        				Nostress_Nscexport_Helper_Data::updateConfigFields($oldModel->getSearchengine());       			
				}
				else 
				{
					$model->setUrl(Mage::helper('nscexport')->__('XML file doesnt exist'));					
					$model->save();
					
				}	
				//Get nearest export id
				$model->save();
				
				
				Nostress_Nscexport_Helper_Data::updateConfigFields($model->getSearchengine());

				Nostress_Nscexport_Helper_Data::updateCategoryProducts($model->getId(),$model->getCategoryProductIds(),$model->getStoreId());				
        		        		
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nscexport')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) 
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nscexport')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() 
	{
		if( $this->getRequest()->getParam('id') > 0 ) 
		{
			try 
			{
				Nostress_Nscexport_Helper_Data::deleteProfile($this->getRequest()->getParam('id'));
				
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
    
	public function categoriesJsonAction()    
	{    					
		if ($this->getRequest()->getParam('expand_all')) 
		{            
			Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);        
		} 
		else 
		{            
			Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);        
		}   
				
		$category = $this->_initCategory();
		//$this->getResponse()->setBody(                
		//		$this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_tab_wizard')->getCatTreeJson($category));
		$this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')->getTreeJson($category));
		//return;
		/*
		if ($categoryId = (int) $this->getRequest()->getPost('id')) 
		{            
			$this->getRequest()->setParam('id', $categoryId);            
			if (!$category = $this->_initCategory()) 
			{                
				return;            
			}            
			$this->getResponse()->setBody(                
				$this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_tab_wizard')->getCatTreeJson($category));		        
		} */   
	}	
	
	protected function _initCategory()
    {    	    	
        $categoryId = (int) $this->getRequest()->getParam('id');                
        //$storeId    = (int) $this->getRequest()->getParam('store');

        $category = Mage::getModel('catalog/category');
        // $category->setStoreId(0);
		
        $category->load($categoryId);
        return $category;
        
        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore(0)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                    return false;
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }
    
	public function docsAction() 
	{		
		$this->getResponse()->setRedirect(Mage::helper('nscexport')->__('https://docs.nostresscommerce.cz/en/wiki/export_en'));
		return $this;
	}
	
    public function massDeleteAction()
    {
        $profileIds = $this->getRequest()->getParam('profile');
        if (!is_array($profileIds)) {
            $this->_getSession()->addError($this->__('Please select profile(s)'));
        }
        else {
            try {
                foreach ($profileIds as $profileId) 
                {
                	Nostress_Nscexport_Helper_Data::deleteProfile($profileId);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($profileIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massGenerateAction()
    {
        $profileIds = $this->getRequest()->getParam('profile');
        if (!is_array($profileIds)) {
            $this->_getSession()->addError($this->__('Please select profile(s)'));
        }
        else 
        {
            try 
            {            	
                foreach ($profileIds as $profileId) 
                {
                	Nostress_Nscexport_Helper_Data::generateProfile($profileId);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully generated', count($profileIds))
                );
            } catch (Exception $e) 
            {
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
		$block = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_grid', 'nscexport_catalog_category_products');
		$this->getResponse()->setBody($block->toHtml().$block->getChosenProductsHtml());
		//* tento kod by bylo vhodne refaktorizovat do nejakeho helperu
		//* pouziva se jeste v nejakych gridech
/*		$path =  $this->getRequest()->getParam('path', 'root');
		$path = preg_replace('/_/', '/', $path);
		
		$rada_id = null;
		$skupina_id = null;
		
		if ( !$path || $path == 'root' ) {
			
		} else if ( is_numeric($path) ) {
			$rada_id = (int) $path;
		} else if ( preg_match("/\//", $path) ) {
			list( $rada_id, $skupina_id ) = split("/",$path);
		}
		
		$id = $this->getRequest()->getParam('id');
		$zakaznik = Mage::getModel('datovymodel/zakaznik')->load($id);
		Mage::register('zakaznik', $zakaznik);

		$grid_block = $this->getLayout()->createBlock('zakaznik/zakaznik_edit_tab_komponenty_grid', 'komponentaGrid');
		$grid_block->setZobrazCheckbox(Mage::getSingleton('admin/session')->isAllowed('zakaznik/edit/komponenty_tab/ulozit'))
			->setSkupinyPath( $this->getRequest()->getParam('path', 'root'));
			
		$this->getResponse()->setBody(
			$grid_block->toHtml()
		);*/
			
	}
}