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
* @category Nostress
* @package Nostress_Nscexport
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_generalConfig;
    protected $_feedConfig;
    protected $_productConfig;
    protected $_uploadConfig;
	protected $_profile;
	protected $_feed;
	protected $_config;
	protected $_type;
	protected $_file;
	
	const COMMON_PATH = "common";
	
	public function getProfile() {
		if (!$this->_profile) {
			$this->_profile = Mage::registry('nscexport_profile');
		}
		return $this->_profile;
	}
	
	public function getConfig() {
		if (!$this->_config) {
			$this->_config = Mage::registry('nscexport_profile_config');
		}
		return $this->_config;
	}
	
    public function getGeneralConfig() {
		if (!$this->_generalConfig) {
			$config = $this->getConfig();
			if(is_array($config) && array_key_exists("general",$config))
			    $this->_generalConfig = $config["general"];
			else
			    $this->_generalConfig = array();
		}
		return $this->_generalConfig;
	}
	
    public function getFeedConfig() {
		if (!$this->_feedConfig) {
			$config = $this->getConfig();
			if(is_array($config) && array_key_exists("feed",$config))
			    $this->_feedConfig = $config["feed"];
			else
			    $this->_feedConfig = array();
		}
		return $this->_feedConfig;
	}
	
	public function getUploadConfig() {
	    if (!$this->_uploadConfig) {
	        $config = $this->getConfig();
	        if(is_array($config) && array_key_exists(Nostress_Nscexport_Model_Profile::UPLOAD,$config))
	            $this->_uploadConfig = $config[Nostress_Nscexport_Model_Profile::UPLOAD];
	        else
	            $this->_uploadConfig = array();
	    }
	    return $this->_uploadConfig;
	}
	
    public function getProductConfig()
    {
		if (!$this->_productConfig)
		{
			$config = $this->getConfig();
			if(is_array($config) && array_key_exists("product",$config))
			{
			    $productConfig = $config["product"];
			    if(isset($productConfig['types']))
			    {
			        $productConfig['types'] = explode(",",$productConfig['types']);
			    }
			    $this->_productConfig = $productConfig;
			}
			else
			    $this->_productConfig = array();
		}
		return $this->_productConfig;
	}
	
	public function getAttributeConfig()
	{
		if (!$this->_attributeConfig) {
			$config = $this->getConfig();
			if(array_key_exists("attribute_filter",$config))
			    $this->_attributeConfig = $config["attribute_filter"];
			else
			    $this->_attributeConfig = array();
		}
		return $this->_attributeConfig;
	}
	
	public function getAttributeValue($branch, $attribute = null)
	{
		$feedConfig = $this->getFeedConfig();
	
		if ($attribute != null && isset($feedConfig[$branch][$attribute]))
		{
			return $feedConfig[$branch][$attribute];
		}
		else
		{
			return "";
		}
	}
	
	public function getStoreName($storeId = null) {
		$store = null;
		if (!$storeId) {
			$store = $this->getStore();
		}
		else {
			$store = Mage::app()->getStore($storeId);
		}
		return Mage::helper('nscexport')->getFullStoreName($store);
	}
	
	public function getFeedName($feedId = null) {
		if (!$feedId) {
			return $this->getFeed()->getLink();
		}
		return Mage::getModel("nscexport/feed")->getFeedByCode($feedId)->getLink();
	}
	
	protected function getConfigField($index,$dataIndex = null)
	{
		if(!isset($dataIndex))
			$dataIndex = $index;
					
		$result = null;
		if (Mage::registry('nscexport_data')) {
			$result = Mage::registry('nscexport_data')->getData($dataIndex);
		}
		if (!$result) {
			$result = $this->getValue($index);
		}
		if (!$result) {
			$result = $this->getRequest()->getParam($index);
		}
		return $result;
	}
	
	public function getType()
	{
		if (!$this->_type)
		{
			$this->_type = $this->getConfigField("type");
		}
		return $this->_type;
	}
	
	public function getFile()
	{
		if (!$this->_file) {
			$this->_file = $this->getConfigField("file");
		}
		return $this->_file;
	}
	
	public function getFeed()
	{
		if (!$this->_feed)
		{
			$feedId = $this->getConfigField("feed");
			$this->_feed = Mage::getModel("nscexport/feed")->getFeedByCode($feedId);
		}
		return $this->_feed;
	}
	
	public function getStore()
	{
		if (!$this->_store)
		{
			$storeId = (int)$this->getConfigField("store","store_id");
			$this->_store = Mage::app()->getStore($storeId);
		}
		return $this->_store;
	}
	
	protected function arrayField($array,$key,$default = "")
	{
		if(is_array($array) && array_key_exists($key,$array))
			return $array[$key];
		else
			$default;
	}
	
    /**
     * Create buttonn and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $id
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class='', $id=null) {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $label,
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $id,
            ))
            ->toHtml();
    }
    
    public function getHelpButtonHtmlByFieldset($fieldsetId)
    {
    	$targetUrl = Mage::helper('nscexport')->getHelpUrl($fieldsetId);
    	return $this->getHelpButtonHtml($targetUrl);
    }
    
    public function getHelpButtonHtml($targetUrl)
    {
    	$label = Mage::helper('nscexport')->__('Get Help!');
    	$class = 'scalable go';
    	$onclick = "window.open('{$targetUrl}')";
    	return $this->getButtonHtml($label,$onclick, $class, $id=null);
    }
}
