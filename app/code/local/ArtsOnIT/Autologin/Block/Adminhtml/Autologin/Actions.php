<?php
/**
 * ArtsOnIT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://www.mageext.com/respository/docs/License-SourceCode.pdf
 *
 * @category   ArtsOnIT
 * @package    ArtsOnIT_Autologin
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 ArtsonIT di Calore (http://www.mageext.com)
 * @license    http://www.mageext.com/respository/docs/License-SourceCode.pdf
 */
class ArtsOnIT_Autologin_Block_Adminhtml_Autologin_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
     public function __construct() {
        
    }
 
    public function render(Varien_Object $row)
    {
        $edit = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit',array('id'=>$row->getId()));
        $html = '<a href="'. $edit  .'">'. Mage::helper('autologin')->__('Edit') .'</a>';

        if($row->getData('autologin_is_active'))
		{
			$customer = Mage::getModel('customer/customer')->load($row->getId());
			$url = Mage::getUrl('customer/account', array(
				'_autologin'=> true,
				'_autologin_customer'=> $customer,
				'_store' => $customer->getStoreId(),
				'_forced_secure' => true
			));
         	$html .= ' - <a href="'. $url  .'" target="_blank">'. Mage::helper('autologin')->__('Log In') .'</a>';
         
		}
        return $html;
    }


}
