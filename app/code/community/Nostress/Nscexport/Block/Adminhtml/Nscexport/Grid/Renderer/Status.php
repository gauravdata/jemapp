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
 */

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	const STATUS_CLASS_COLOR_GREEN = 'grid-severity-notice';
	const STATUS_CLASS_COLOR_ORANGE = 'grid-severity-major';
	const STATUS_CLASS_COLOR_RED= 'grid-severity-critical';
	
    public function render(Varien_Object $row)
    {
		return $this->decorateStatus($row->getMessage(), $row);
    }
    
    /**
     * Decorate status column values
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    protected function decorateStatus($value, $row)
    {
    	if(!$row->getEnabled())
    		return $this->getStatusHtml(self::STATUS_CLASS_COLOR_ORANGE,"Disabled");
    	$class = '';
    	$additionalMessage = "";
    	$isDebugMode = Mage::helper('nscexport')->isDebugMode();
    	$originalValue = $value;
    
    	switch ($row->getStatus()) {
    		case Nostress_Nscexport_Model_Profile::STATUS_FINISHED :
    			$class = self::STATUS_CLASS_COLOR_GREEN;
    			if(!empty($value))
    			{
    				$value = $this->__("Finished.")." ";
    				$value .= $this->extractStatisticsString($originalValue,"Products")." ";
    				$value .= $this->extractStatisticsString($originalValue,"Categories")." ";
    				$value .= $this->extractStatisticsString($originalValue,"Upload")." ";
    					
    				$type = $this->translateType("Time");
    				$index = strpos($originalValue,$type);
    				if($isDebugMode)
    					$additionalMessage = substr($originalValue, $index);
    			}
    			else
    			{
    				$class = self::STATUS_CLASS_COLOR_ORANGE;
    				$value = $this->__("Not executed");
    			}
    			break;
    		default:
    			$class = self::STATUS_CLASS_COLOR_RED;
    			$additionalMessage = $originalValue;
    			$value = $this->__("Error occured");
    			break;
    	}
    	$html = $this->getStatusHtml($class,$value);
    	$html .= $additionalMessage;
    	return $html;
    }
    
    protected function extractStatisticsString($message,$originalType)
    {
    	$matches = array();
	    $type = $this->translateType($originalType);
	    if(preg_match('/'.$type.': [0123456789OK]+/',$message,$matches))
		    return $matches[0];
	    else if(preg_match('/'.$originalType.': [0123456789OK]+/',$message,$matches))
	    	return $matches[0];
    	return "";
    }
    
    protected function translateType($type)
    {
    	$translation = "";
    	switch($type)
    	{
    		case "Products":
    			$translation = $this->__("Products: %s ",0);
    			break;
    		case "Categories":
    			$translation = $this->__("Categories: %s ",0);
    			break;
    		case "Time":
    			$translation =$this->__("Time: %s Memory: %s ",0,0);
    			break;
    		case "Upload":
    		    $translation = $this->__('Upload: %s', 0);
    		    break;
    	}
    	$index = strpos($translation, ":");
    	$translation = substr($translation, 0,$index);
    	return $translation;
    }
    
    protected function getStatusHtml($class,$value)
    {
    	$value = $this->__($value);
    	return  '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }
}
