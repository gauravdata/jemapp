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
 *
 * @category Nostress
 * @package Nostress_Nscexport
 */

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit_Form extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Abstract
{
	/**
	* Additional buttons on profile page
	*
	* @var array
	*/
	protected $_additionalButtons = array();
	
	public function __construct() {
		parent::__construct();
		$this->setTemplate('nscexport/edit/form.phtml');
	}
	
	protected function _prepareLayout() {
		$profile = $this->getProfile();
		$profileId = (int)$profile->getId();
		
		if ($profileId == 0)
		{
			$backLocation = $this->getUrl("*/*/new");
		}
		else
		{
			$backLocation = $this->getUrl("*/*/index");
		}

		$this->setChild('tabs',
			$this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tabs', 'tabs')
		);
		
		// Back button
		$this->setChild('back_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label' => Mage::helper('nscexport')->__('Back'),
					'onclick' => "setLocation('".$backLocation."');",
					'class' => 'back',
				))
		);
		
		// Save button
		$this->setChild('save_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label' => Mage::helper('nscexport')->__('Save Profile'),
					'onclick' => "updateOutput();profileSubmit('".$this->getSaveUrl()."', false)",
					'class' => 'save'
				))
		);
		
		$this->setChild('save_and_continue',
		        $this->getLayout()->createBlock('adminhtml/widget_button')
		        ->setData(array(
	                'label' => Mage::helper('nscexport')->__('Save and Continue Edit'),
	                'onclick' => "updateOutput();$('profile_edit_form').action = $('profile_edit_form').action+'back/edit/'; profileSubmit('neco', false)",
	                'class' => 'save'
		        ))
		);
		
		// Delete button
		if ($profileId != 0)
		{
			$this->setChild('delete_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label' => Mage::helper('catalog')->__('Delete Profile'),
					'onclick' => "setLocation('".$this->getUrl('*/*/delete', array('_current' => true))."', true, {$profileId})",
					'class' => 'delete'
				))
			);
			
			if(!$profile->isUploadable())
			{
			    $disabled = "disabled";
			    $class = "disabled";
			    $onclick = "";
			} else {
			    $class = "save";
			    $disabled = "";
			    $onclick = "setLocation('".$this->getUrl('*/*/uploadFeed', array('id'=>$profileId))."')";
			}
			
			$this->setChild('upload_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
			        ->setData(array(
		                'label' => Mage::helper('catalog')->__('Upload via FTP'),
		                'onclick' => $onclick,
		                'class' => $class,
			            'disabled' => $disabled
                    ))
			);
		}
		
		$this->addLiveChatButton();
		
// 		$categories = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_grid', 'nscexportCatalogCategoryProducts');
// 		$this->setChild('nscexportCatalogCategoryProducts', $categories);
		
		return parent::_prepareLayout();
	}
	
	protected function addLiveChatButton() {
	
	    $this->setChild('livechat_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData( $this->helper('nscexport')->getLivechatButtonOptions())
	    );
	}
	
	public function getBackButtonHtml() {
		return $this->getChildHtml('back_button');
	}
	
	public function getDeleteButtonHtml() {
		return $this->getChildHtml('delete_button');
	}
	
	public function getUploadButtonHtml() {
	    return $this->getChildHtml('upload_button');
	}
	
	public function getSaveButtonHtml() {
		return $this->getChildHtml('save_button');
	}
	public function getSaveAndContinueButtonHtml() {
	    return $this->getChildHtml('save_and_continue');
	}
	public function getLivechatButtonHtml() {
	    return $this->getChildHtml( 'livechat_button');
	}
	
	public function getXpathColumnHtml()
	{
		$feed = $this->getProfile()->getFeedObject();
		
		$xpathColum = "";
		if($feed->getFileType() == 'xml')
		{
			$xpathColum = '<td>ITEM ROOT<input type="hidden" name="feed[attributes][attribute][{numrows}][path]" value="ITEM ROOT" /></td>';
		}
		return $xpathColum;
	}
	
	public function getFileType()
	{
		return $this->getProfile()->getFeedObject()->getFileType();
	}
	
	public function getFeedCode()
	{
		return $this->getProfile()->getFeed();
	}
	
	public function getCustomAttributeIndexOffset()
	{
		return Nostress_Nscexport_Helper_Data::CUSTOM_ATTRIBUTE_ROW_INDEX_OFFSET;
	}
	
	/**
	* Retrieve additional buttons html
	*
	* @return string
	*/
	public function getAdditionalButtonsHtml() {
		$html = '';
		foreach ($this->_additionalButtons as $childName) {
			$html .= $this->getChildHtml($childName);
		}
		return $html;
	}
	
	/**
	* Add additional button
	*
	* @param string $alias
	* @param array $config
	* @return Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit_Form
	*/
	public function addAdditionalButton($alias, $config) {
		if (isset($config['name'])) {
			$config['element_name'] = $config['name'];
		}
		$this->setChild($alias . '_button', $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config));
		$this->_additionalButtons[$alias] = $alias . '_button';
		return $this;
	}
	
	/**
	* Remove additional button
	*
	* @param string $alias
	* @return Nostress_Nscexport_Block_Adminhtml_Nscexport_Edit_Form
	*/
	public function removeAdditionalButton($alias) {
		if (isset($this->_additionalButtons[$alias])) {
			$this->unsetChild($this->_additionalButtons[$alias]);
			unset($this->_additionalButtons[$alias]);
		}
		return $this;
	}
	
	public function getStoreId() {
		return $this->getProfile()->getStoreId();
	}
	
	public function getTabsHtml() {
		return $this->getChildHtml('tabs');
	}
	
	public function getHeader() {
		$profile = Mage::registry('nscexport_profile');
		if (isset($profile) && $profile->getId()) {
			return Mage::helper('nscexport')->__('Koongo Connector')." - ".Mage::helper('nscexport')->__('Edit Profile')." '".$this->htmlEscape($profile->getName())."'";
		}
		else {
			return Mage::helper('nscexport')->__('Koongo Connector')." - ".Mage::helper('nscexport')->__('New Profile').' ('.Mage::helper('nscexport')->__('Step').' 2)';
		}
	}
	
	public function isAjax() {
		return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
	}
}