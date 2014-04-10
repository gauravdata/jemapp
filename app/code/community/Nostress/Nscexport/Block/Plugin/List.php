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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 * 
 * @copyright  Copyright (c) 2010 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */
/**
 * 
 * @category   Nostress
 * @package    Nostress_Nscexport
 * @author     NoStress Commerce Team <info@nostresscommerce.cz>
 */
class Nostress_Nscexport_Block_Plugin_List extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected $_helper;
	const STATUS_PLUGIN_NOT_INSTALLED = 1;
	const STATUS_PLUGIN_UPDATE_REQUEST = 2;
	const STATUS_MODULE_UPDATE_REQUEST = 3;
	const STATUS_OK = 4;
	
	const CELL_NAME = "name";
	const CELL_CUR_VERSION = "current_verison";
	const CELL_LAT_VERSION = "latest_verison";	
	const CELL_CUR_MODULE_VERSION = "current_mod_version";
	const CELL_STATUS = "status";
    
	public function render(Varien_Data_Form_Element_Abstract $element)
    { 	
    	$html = "";
    	$collection = Mage::getModel('nscexport/plugin')->getCollection()->load();
    	foreach ($collection as $item) 
    	{
    		$item = $this->checkModuleVersion($item);   
    		$item = $this->addCurrentVersion($item);   		    		    				
    		$html .= $this->getEntryHtml($item);    		
    	}  
    	return $html; 	            	    	
    }   
    
	public function getEntryHtml($item) 
	{
		$html = "<tr>";
		$html.= "<td>{$this->processCell($item,self::CELL_NAME)}</a></td>";
		$html.= "<td width=\"350px\">{$item->getDescription()}</td>";
		$html.= "<td>{$this->processCell($item,self::CELL_CUR_VERSION)}</td>";
		$html.= "<td>{$this->processCell($item,self::CELL_LAT_VERSION)}</td>";
		$html.= "<td>{$this->processCell($item,self::CELL_CUR_MODULE_VERSION)}</td>";		
		$html.= "<td>{$this->processCell($item,self::CELL_STATUS)}</td>";
		$html .= "</tr>";		
		return $html;
	}
	
	protected function addCurrentVersion($item)
	{
		$curVersion = $this->nscHelper()->getPluginVersion($item->getCode());
    	$item->setCurrentVersion($curVersion);    
    	if(empty($curVersion))
    		$item->setStatus(self::STATUS_PLUGIN_NOT_INSTALLED);
    	else
    	{
    		if($this->nscHelper()->cmpVersions($curVersion,$item->getLatestVersion()))
    			$item->setStatus(self::STATUS_PLUGIN_UPDATE_REQUEST); 
    	}
		return $item;
	}
	
	protected function checkModuleVersion($item)
	{
		if(!Mage::helper('nscexport/version')->isLatestVersionInstalled())
			$item->setStatus(self::STATUS_MODULE_UPDATE_REQUEST);
		return $item;
	}
	
	protected function processCell($item,$cellName)
	{
		$html = "";
		switch($cellName)
		{
			case self::CELL_NAME:
				$html = $this->addAnchor($item->getName(),$item->getDownloadLink()); 
				break;
			case self::CELL_CUR_VERSION:
		    	if($item->getStatus() == self::STATUS_PLUGIN_NOT_INSTALLED)
		    		$html = $this->addColorSpan($this->__("Not Installed"),false);
		    	else
	    	  		$html = $this->addColorSpan($item->getCurrentVersion(),$item->getStatus() != self::STATUS_PLUGIN_UPDATE_REQUEST);
		    	break;
			case self::CELL_LAT_VERSION:
					$html = $this->addColorSpan($item->getLatestVersion(),true);
				break;
			case self::CELL_CUR_MODULE_VERSION:
				$html = $this->addColorSpan($item->getModuleVersion(),	Mage::helper('nscexport/version')->isLatestVersionInstalled());
				break;
			case self::CELL_STATUS:
				$html = $this->getStatusHtml($item);
				break;
		}
		return $html;
	}
	
	protected function getStatusHtml($item)
	{
		$html = "";
		$ok = false;
		switch ($item->getStatus())
		{
			case self::STATUS_PLUGIN_NOT_INSTALLED:
				$html = $this->addAnchor($this->__("Get this plugin"),$item->getDownloadLink());				
				break;
			case self::STATUS_PLUGIN_UPDATE_REQUEST:
				$html = $this->addAnchor($this->__("Update this plugin"),$this->helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Version::PLUGIN_UPDATE_LINK));
				break;
			case self::STATUS_MODULE_UPDATE_REQUEST:
				$html = $this->addAnchor($this->__("Update Koongo Connector"),$this->helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Version::MODULE_UPDATE_LINK));
				break;
			default:
				$html = $this->__("OK");
				$ok = true;
			break;
		}
		$html = $this->addColorSpan($html,$ok);
		return $html;
	}
	
	protected function addAnchor($content,$link)
	{
		return "<a target='_blank' href='{$link}'>{$content}<a>";
	}
	
	protected function addColorSpan($content,$isOk)
	{
		$color = "";
		if($isOk === true)
			$color = "color:green;";
		else if($isOk === false)
			$color = "color:red;";
		return "<span style='{$color}font-weight:bold'>{$content}</span>";
	}
	
	protected function nscHelper()
	{
		if(!isset($this->_helper))
			$this->_helper = Mage::helper('nscexport');
		return $this->_helper;
	}	
}