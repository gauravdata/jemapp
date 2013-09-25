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
 * @copyright  Copyright (c) 2008 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */
/**
 * 
 * @category   Nostress
 * @package    Nostress_Nscexport
 * @author     NoStress Commerce Team <info@nostresscommerce.cz>
 */

class Nostress_Nscexport_Helper_Version extends Mage_Payment_Helper_Data
{
	protected $_moduleLabel = 'Nscexport';
	protected $_moduleVersion = '4.0.0.2';
	protected $_validForVersion = '1.5';	
	protected $_s = 'mage123';
	
	public function getModuleStatusHtml()
	{
		if($this->isVersionValid()){
			return "<span style='color:green;font-weight:bold'>".$this->__('OK')."</span>";			
		}else{
			return "<span style='color:red;font-weight:bold'>".$this->__('Not Valid')." - <a href=\"".$this->__('http://www.nostresscommerce.com/buy-new-license.html')."\">".$this->__('Buy New License')."</a></span>";
		}
	}
	
	public function getLicenseKeyStatusHtml()
	{
		if(!$this->getLicenseKey()){
			return "<span style='color:red;font-weight:bold'>".$this->__('Please enter the License key')."</span>";
		}
		if($this->isLicenseKeyValid()){
			return "<span style='color:green;font-weight:bold'>".$this->__('OK')."</span>";			
		}else{
			return "<span style='color:red;font-weight:bold'>".$this->__('Not Valid')." - <a href=\"".$this->__('http://www.nostresscommerce.com/buy-new-license.html')."\">".$this->__('Buy New License')."</a></span>";
		}
	}
	
	public function isModuleValid(){
		return $this->isVersionValid() && $this->isLicenseKeyValid();
	}
	
	public function isVersionValid(){
		return substr(Mage::getVersion(),0,3) == $this->_validForVersion;
	}
	
	public function isLicenseKeyValid(){
		return $this->getLicenseKey() ===  $this->generateLicenseKey();
	}
	
	public function getLicenseKey(){
		return Mage::getStoreConfig('nostress_modules/nostress_dashboard/license_key_'.$this->_getModuleName());
	}
	
	public function generateLicenseKey(){	
		$serverName = $this->getServerName();	
		return md5(sha1($serverName.$this->_getModuleName().substr($this->_moduleVersion,0,1).$this->_s));
	}
	
	public function getInvalidModuleConfigHtml(){
		$url = Mage::getModel('adminhtml/url');
		
		$elementId = $this->_getModuleName();
		
		$html = "<div class=\"entry-edit-head collapseable\" >";
    	$html .= "<a id=\"{$elementId}-head\" href=\"#\" onclick=\"Fieldset.toggleCollapse('{$elementId}', '".$url->getUrl('*/*/state')."'); return false;\">{$this->getModuleLabel()}</a>";
    	$html .= "</div>";
    	$html .= "<input id=\"{$elementId}-state\" name=\"config_state[{$elementId}]\" type=\"hidden\" value=\"0\" />";
    	$html .= "<fieldset class=\"config collapseable\" id=\"{$elementId}\"><legend>{$this->getModuleLabel()}</legend>";
    	$html .= "<span style='color:red;font-weight:bold'>".$this->__('Your License is not valid')." - <a href=\"".$this->__('http://www.nostresscommerce.com/buy-new-license.html')."\">".$this->__('Buy New License')."</a></span>";
    	$html .= "<br/>";
    	$html .= $this->__('Module is deactivated, more information is available');
    	$html .= " <a href=\"".$url->getUrl('adminhtml/system_config/edit',array('section'=>'nostress_modules'))."\">".$this->__('here')."</a>";
    	$html .= "</fieldset>";
    	$html .= Mage::helper('adminhtml/js')->getScript("Fieldset.applyCollapse('{$elementId}')");
    	return $html; 
	}
	
	public function getDashboardFooterHtml(){
		return 
    	"<tr>
    		<td colspan='4'>&copy; <a href=\"".$this->__("http://www.nostresscommerce.com")."\">NoStress Commerce</a> 2010</td>    			
    	</tr></table>"
    	.Mage::helper('adminhtml/js')->getScript("            
            $('nostress_modules_nostress_dashboard-head').setStyle('background: none;');
            $('nostress_modules_nostress_dashboard-state').value = 1;
            $('nostress_modules_nostress_dashboard-head').writeAttribute('onclick', 'return false;');
            $('nostress_modules_nostress_dashboard').show();
        ");
	}
	
	public function getDashboardHeaderHtml(){
		return 
    	"<table cellspacing=\"15\"><tr>
    		<th>{$this->__('Module Name')}</th>
    		<th>{$this->__('Module Version')}</th>
    		<th>{$this->__('Your License for Magento')}</th>
    		<th>{$this->__('Module Status')}</th>
    		<th>{$this->__('Enter License Key')}</th> 
    		<th>{$this->__('License Status')}</th>    		
    	</tr>";
	}
	
	public function getDashboardEntryHtml(){
		$url = Mage::getModel('adminhtml/url');
		return 
    	"<tr>
    		<td><a href=\"".$url->getUrl('*/*/*',array('section'=>'nscexport'))."#{$this->_getModuleName()}-head\">{$this->getModuleLabel()}</a></td>
    		<td>{$this->_moduleVersion}</td>
    		<td>{$this->_validForVersion}.x</td>
    		<td>{$this->getModuleStatusHtml()}</td>
    		<td><input id=\"{$this->_getModuleName()}_licensekey\" class=\"input-text\" type=\"text\" value=\"".$this->getLicenseKey()."\" name=\"groups[nostress_dashboard][fields][license_key_{$this->_getModuleName()}][value]\" /></td>
    		<td>{$this->getLicenseKeyStatusHtml()}</td>    		
    	</tr>";
	}
	
	public function getModuleLabel(){
		return $this->__($this->_moduleLabel);
	}
	
	public function validateLicenceBackend()
	{
		if(!self::isLicenseKeyValid())
		{
			Mage::log("Module is not valid. Domain name:". $this->getServerName());
			Mage::getSingleton('adminhtml/session')->addError($this->__('Your License is not valid. ').$this->__('Module %s is deactivated!',$this->getModuleLabel()));
			header("Location: ".Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('adminhtml/system_config/edit',array('section'=>'nostress_modules')));
			exit;
		}
		return true;	
	}
	
	private function getServerName()
	{
		$serverName = $_SERVER['SERVER_NAME'];
		if(!isset($serverName) ||  $serverName == "")
		{
			$unsecureBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);			
			$unsecureBaseUrl = str_replace("http://","",$unsecureBaseUrl);
			$unsecureBaseUrl = str_replace("https://","",$unsecureBaseUrl);
			
			$index = strpos($unsecureBaseUrl,"/");
			$serverName = substr($unsecureBaseUrl,0,$index);		
		}
		return $serverName;
	}
}
