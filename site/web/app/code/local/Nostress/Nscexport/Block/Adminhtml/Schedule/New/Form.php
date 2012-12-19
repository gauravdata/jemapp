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

class Nostress_Nscexport_Block_Adminhtml_Schedule_New_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
        $form = new Varien_Data_Form(array('id' => 'new_form', 'action' => $this->getData('action'), 'method' => 'post'));
		$fieldset = $form->addFieldset('general', array('legend' => Mage::helper('nscexport')->__('Schedule Data')));

		// Job-Code
		$codeSelect = Mage::getSingleton('nscexport/schedule_code')->toFormElementSelect();

		$fieldset->addField('job_code', 'note', array(
			'label' => Mage::helper('nscexport')->__('Job Code'),
			'title' => Mage::helper('nscexport')->__('Job Code'),
			'class' => 'required-entry',
			'text' => $codeSelect->toHtml(),
		));

		$fieldset->addField('status', 'note', array(
			'label' => Mage::helper('nscexport')->__('Status'),
			'title' => Mage::helper('nscexport')->__('Status'),
			'class' => 'required-entry',
			'text' => Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_PENDING),
		));

		$fieldset->addField('scheduled_at', 'date', array(
			'label' => Mage::helper('nscexport')->__('Scheduled At'),
			'title' => Mage::helper('nscexport')->__('Scheduled At'),
			'html_id' => 'scheduled_at',
			'name' => 'scheduled_at',
			'class' => 'required-entry',
			'format' => Varien_Date::DATETIME_INTERNAL_FORMAT, // hardcode because hardcoded values delimiter
            'time' => true,
			'image' => $this->getSkinUrl('images/grid-cal.gif'),
		));

		#$form->setValues($schedule->getData());
		$form->setUseContainer(true);
		$this->setForm($form);
        return parent::_prepareForm();
	}

}