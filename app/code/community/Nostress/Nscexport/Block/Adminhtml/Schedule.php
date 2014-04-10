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
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Block_Adminhtml_Schedule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * 
	 */
	public function __construct()
	{
		$this->_blockGroup 		= 'nscexport';
        $this->_controller 		= 'adminhtml_schedule';
        $this->_headerText 		= Mage::helper('nscexport')->__('Koongo Connector - Cronjobs - Schedule');

        parent::__construct();
	}

}