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
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/
/**
 * Customized Google Analytics tracking code
 * Version 5.00
 *
 * Copyright (c) 2008-2010 H1.cz s.r.o.
 * Copyright (c) 2010 Medio Interactive, s.r.o.
 * See http://www.h1.cz/ga for more information.
 */
class Nostress_Nscexport_Block_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    /**
     * Prepare and return block's html output
     *
     * @return string
     */
    protected function _toHtml()
    {
    	if(!Mage::helper('nscexport')->getNscGoogleanalyticsStoreConfig('enabled'))
    	{
    		return parent::_toHtml();
    	}
    	
        if (!Mage::getStoreConfigFlag('google/analytics/active')) {
            return '';
        }

        $enginesNamesJsString = ""; 
        $engines = Nostress_Nscexport_Helper_Data::getAllowedEnginesCollection($this->getData('searchengine'));
        $firstTime = true;
        foreach($engines as $code => $engine)
		{
			$engineName = $engine['name'].$engine['suffix'];
			if($firstTime)
				$firstTime = false;
			else
			 	$enginesNamesJsString .= ",";
			$enginesNamesJsString .= "'".strtolower($engineName).":q'";
		}
        
        $this->addText('
<!-- BEGIN GOOGLE NOSTRESS XML FEED EXPORT ANALYTICS CODE -->
<script type="text/javascript">

var _gaq=_gaq||[];(function(){var a=document.createElement(\'script\');a.type=\'text/javascript\';a.async=true;a.src=(\'https:\'==document.location.protocol?\'https://ssl\':\'http://www\')+\'.google-analytics.com/ga.js\';var s=document.getElementsByTagName(\'script\')[0];s.parentNode.insertBefore(a,s)})();var _ga={e:['.
$enginesNamesJsString.  
'],create:function(a,b,c)
{	if(!b)
		{b=\'auto\'}
	if(c)
		{c+=\'.\'}
	else
		{c=\'\'}
	_gaq.push([c+\'_setAccount\',a]);
	_gaq.push([c+\'_setDomainName\',b]);
	_gaq.push([c+\'_setAllowAnchor\',true]);
	var s,i;
	for(i=this.e.length-1;i>=0;i--)
	{
		s=this.e[i].split(\':\');
		_gaq.push([c+\'_addOrganic\',s[0],s[1],true])
    }
    }
    }

</script>
<script type="text/javascript">
_ga.create('.$this->getAccount().');
_gaq.push(["_trackPageview", "'.$this->getPageName().'"]);
</script>

<!-- END GOOGLE NOSTRESS XML FEED EXPORT ANALYTICS CODE -->
        ');

        $this->addText($this->getQuoteOrdersHtml());

        if ($this->getGoogleCheckout()) {
            $protocol = Mage::app()->getStore()->isCurrentlySecure() ? 'https' : 'http';
            $this->addText('<script src="'.$protocol.'://checkout.google.com/files/digital/ga_post.js" type="text/javascript"></script>');
        }

        if (!$this->_beforeToHtml()) {
            return '';
        }

        return $this->getText();
    }
    
  public function getDomainName() 
  { 
  	 $domainName = Mage::helper('nscexport')->getNscGoogleanalyticsStoreConfig('domainname');
     if(isset($domainName) && $domainName != "") 
     {
            return $domainName;
     }
     return 'auto';
  }
}
