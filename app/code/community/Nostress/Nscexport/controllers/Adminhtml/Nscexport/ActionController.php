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

class Nostress_Nscexport_Adminhtml_Nscexport_ActionController extends Mage_Adminhtml_Controller_Action
{
	protected $_helper;

	
	public function preDispatch() {
		parent::preDispatch();
	}
	
	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}
	
	public function indexAction() {
		Mage::helper('nscexport/version')->checkLicense();
		$this->_initAction()->renderLayout();
	}
	
	public function docsAction() {
	    
	    $this->_initAction();
	    $this->renderLayout();
		//$this->getResponse()->setRedirect($this->helper()->__('https://docs.koongo.com/display/KoongoConnector/Koongo+Connector'));
		return $this;
	}
	
	public function customFeedRedirectAction() {
		$this->getResponse()->setRedirect($this->helper()->__('https://docs.koongo.com/display/KoongoConnector/Custom+Feed+Layout'));
		return $this;
	}
	
	public function supportContactRedirectAction() {
		$this->getResponse()->setRedirect($this->helper()->__('https://store.koongo.com/support-and-contact.html'));
		return $this;
	}
	
	public function getTypeByFeedAction() {
		$feed = $_POST["feed"];
		$types = Mage::helper('nscexport/data_feed')->getTypeOptions($feed);
		
		if (empty($types) || empty($feed)) {
			echo "<option value=\"\">".Mage::helper('nscexport')->__('--Please select Feed--')."</option>";
		}
		
		foreach ($types as $type) {
			echo "<option value=\"".$type["value"]."\">".$type["label"]."</option>";
		}
	}
	
	public function getFileByTypeAction() {
		$feed = $_POST["feed"];
		$type = $_POST["type"];
		$files = Mage::helper('nscexport/data_feed')->getFileOptions($type, $feed);
		
		if (empty($files) || empty($type)) {
			echo "<option value=\"\">".Mage::helper('nscexport')->__('--Please select Type--')."</option>";
		}
		
		foreach ($files as $file) {
			echo "<option value=\"".$file["value"]."\">".$file["label"]."</option>";
		}
	}
	
	public function newConditionHtmlAction()
	{
	    $id = $this->getRequest()->getParam('id');
	    $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
	    $type = $typeArr[0];
	    
	    $store = $this->getRequest()->getParam( 'store');
	    if( $store) {
	        Mage::register('nscexport_store', $store);
	    }
	    
	    $feedCode = $this->getRequest()->getParam( 'feed_code');
	    if($feedCode)
	    {
		    if(Mage::registry('nsc_current_feed_code'))
		    	Mage::unregister('nsc_current_feed_code');
		    Mage::register('nsc_current_feed_code',$feedCode);
	    }
	
	    $form = $this->getRequest()->getParam('form');
	
	    $model = Mage::getModel($type)
	        ->setId($id)
	        ->setType($type)
	        ->setRule(Mage::getModel('nscexport/rule'))
	        ->setPrefix($form)
	        ;
	    if (!empty($typeArr[1])) {
	        $model->setAttribute($typeArr[1]);
	    }
	
	    if ($model instanceof Mage_Rule_Model_Condition_Abstract
	            || $model instanceof Mage_Rule_Model_Action_Abstract) {
	        $model->setJsFormObject($form);
	        $html = $model->asHtmlRecursive();
	    } else {
	        $html = '';
	    }
	    $this->getResponse()->setBody($html);
	}
	
    /**
     * Reload engines taxonomy table
     */
    public function reloadpluginlistAction()
    {
    	try
    	{
    	    $client = Mage::helper('nscexport/data_client');
    	    $client->updatePlugins();
    	    $client->updateLicense();
    		$this->_getSession()->addSuccess($this->__('Plugin List reloaded.'));
        }
        catch (Exception  $e)
        {
        	$message = $this->helper()->__("Plugin List load failed: ");
        	$this->_getSession()->addError($message. $e->getMessage());
        }
        $this->_redirect('adminhtml/system_config/edit/section/koongo_license_and_plugins');
    }
    
	public function getMagentoAttributesAction()
	{
		$index = $_POST["index"];
		$store_id = $_POST["store_id"];
		$feedCode = $_POST["feed_code"];
		$form = new Varien_Data_Form();
		$attributesConfig = array(
			"id" => "nscexport_magentoattribute",
			"name" => "feed[attributes][attribute][".$index."][magento]",
			"style" => "width: 100%;",
			"html_id" => '_'.$index,
			'onchange' => 'showWarning(\'TO_BE_REPLACED_WITH_ID\',true);',
			"values" => Mage::helper('nscexport/data_feed')->getAttributeOptionsAll($store_id,$feedCode)
		);
		
		$attributesSelect = new Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Attributeselect($attributesConfig);
		$attributesSelect->setForm($form);
		echo $attributesSelect->toHtml();
	}
	
	public function getParentSelectAction() {
		$index = $_POST["index"];
		$form = new Varien_Data_Form();
		$parentConfig = array(
			"id" => "nscexport_parentconfig",
			"name" => "feed[attributes][attribute][".$index."][eppav]",
			"style" => "width: 100%;",
			"values" => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		);
		
		$parentSelect = new Varien_Data_Form_Element_Select($parentConfig);
		$parentSelect->setForm($form);
		
		echo $parentSelect->toHtml();
	}
	
	public function getPostConfigAction() {
		$index = $_POST["index"];
		$fileType = "xml";
		if(isset($_POST["file"]))
			$fileType = $_POST["file"];
		$form = new Varien_Data_Form();
		$postConfig = array(
			"id" => "nscexport_postconfig",
			"name" => "feed[attributes][attribute][".$index."][postproc][]",
			"style" => "width: 100%;",
			"values" => Mage::helper('nscexport/data_feed')->getPostProcessFunctionOptions($fileType),
		);
		
		$postSelect = new Varien_Data_Form_Element_Multiselect($postConfig);
		$postSelect->setSize(3);
		$postSelect->setForm($form);
		
		echo $postSelect->toHtml();
	}
	
	public function getTranslateAction() {
		$index = $_POST["index"];
		$translateGrid = $this->loadLayout()->getLayout()->createBlock('nscexport/adminhtml_nscexport_helper_form_attributes_translate_grid');
		$form = new Varien_Data_Form();
		$translateGrid->setData(array(
			'values' => null,
			'row_index' => $index,
			'custom_attribute_array_path_full' => 'feed[attributes][attribute]',
			'isDisabled' => false,
			'attribute' => 'translate',
			'element' => $form,
			'isDisabled' => false
			
		));
		echo $translateGrid->toHtml();
	}
	
	public function addAttributesToProductFlatAction()
	{
		$params = $this->getRequest()->getParam('params');
		if(empty($params))
		{
			$this->_getSession()->addSuccess($this->__('Missing attributes, which have to be added into Product Flat Catalog.'));
			$this->_redirect('*/*/');
		}
		
		$params = str_replace(" ","",$params);
		$params = explode(",", $params);
					
		$this->helper()->setEavAttributesPropertyValue($params,'used_in_product_listing',"1");
		$this->reindexFlatCatalogProductIndex();
		$this->flushAllAction();
		$this->_getSession()->addSuccess($this->__('The attributes have been added to Product Flat Catalog. Run the export profiles with errors again.'));
 		$this->_redirect('*/nscexport_profiles_grid/index');
	}
	
	public function addAttributesToCategoryFlatAction()
	{		
		$this->reindexFlatCatalogCategoryIndex();
		$this->flushAllAction();
		$this->_getSession()->addSuccess($this->__('The attributes have been added to Category Flat Catalog. Run the export profiles with errors again.'));
		$this->_redirect('*/nscexport_profiles_grid/index');
	}
	
	public function enableFlatAction()
	{
		//Enable category flat catalog?
		$this->helper()->enableFlatCatalog();
		$this->_getSession()->addSuccess($this->__('Product and Category Flat Catalog have been enabled.'));
		$this->_redirect('*/nscexport_profiles_grid/index');
	}
	
	protected function reindexFlatCatalogCategoryIndex()
	{
		$process = Mage::getModel('index/process')->load(Nostress_Nscexport_Helper_Data::CATALOG_CATEGORY_FLAT_PROCESS_CODE, 'indexer_code');
		$this->reindexProcess($process);
	}
	
	protected function reindexFlatCatalogProductIndex()
	{
		$process = Mage::helper('catalog/product_flat')->getProcess();
		$this->reindexProcess($process);
		
	}
	
	protected function reindexProcess($process)
	{
		if ($process)
		{
			try
			{
				$this->helper()->reindexProcess($process);
				$this->_getSession()->addSuccess(
					Mage::helper('index')->__('%s index was rebuilt.', $process->getIndexer()->getName())
				);
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->addException($e,
						Mage::helper('index')->__('There was a problem with reindexing process.')
				);
			}
		}
		else
		{
			$this->_getSession()->addError(
					Mage::helper('index')->__('Cannot initialize the indexer process.')
			);
		}
	}
	
	/**
	 * Flush cache storage
	 */
	protected function flushAllAction()
	{
		Mage::dispatchEvent('adminhtml_cache_flush_all');
		Mage::app()->getCacheInstance()->flush();
		$this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("The cache storage has been flushed."));
	}
	
	protected function helper() {
		if (!isset($this->_helper))
			$this->_helper = Mage::helper('nscexport/data_profile');
		return $this->_helper;
	}
}