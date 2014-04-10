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

class Nostress_Nscexport_Block_Adminhtml_Schedule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->_objectId 	= 'schedule_id';
		$this->_blockGroup 	= 'nscexport';
		$this->_controller 	= 'nscexport_schedule_grid';
		
		parent::__construct();

		$this->setTemplate('nscexport/cron/schedule/edit.phtml');
	}

	/**
	 *
	 */
	protected function _prepareLayout()
	{
		$this->setChild('edit_form',
			$this->getLayout()->createBlock('nscexport/adminhtml_schedule_edit_form')
		);

		return parent::_prepareLayout();
	}

	/**
	 *
	 */
	public function getHeaderText()
	{
		$schedule = Mage::registry('nscexport_cron_schedule');
		if( $schedule && $schedule->getId() ) {
			return Mage::helper('nscexport')->__("Edit Item '%s'", $this->__($this->htmlEscape($schedule->getJobCode())));
		} else {
			return Mage::helper('nscexport')->__('Add Item');
		}

	}

	/**
	 *
	 */
	public function getFormHtml()
	{
		return $this->getChildHtml('edit_form');
	}

	/**
	 *
	 */
	public function getBackButtonHtml()
	{
		return $this->getChildHtml('back_button');
	}

	/**
	 *
	 */
	public function getSaveButtonHtml()
	{
		return $this->getChildHtml('save_button');
	}

	/**
	 *
	 */
	public function getDeleteButtonHtml()
	{
		return $this->getChildHtml('delete_button');
	}

	/**
	 *
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete', array('_current'=>true));
	}

}