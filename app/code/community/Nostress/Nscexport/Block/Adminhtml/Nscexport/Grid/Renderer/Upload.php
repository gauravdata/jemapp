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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Grid_Renderer_Upload extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	public function render(Varien_Object $row)
	{
		$url = $this->getUrl('*/nscexport_profiles_grid/uploadFeed', array('id' => $row->getId()));
		$label = Mage::helper('nscexport')->__('Upload via FTP');
		$disabledHtml = "";
		
		if(!$row->isUploadable())
		{
		    $url = "";
		    $disabledHtml = 'disabled="disabled" class="disabled"';
		}
		
		$html = '<form><button onclick="event.stopPropagation();setLocation(\''.$url.'\');" type="button" '.$disabledHtml.' >' .$label. '</button></form>';
		
		return $html;
	}
}
