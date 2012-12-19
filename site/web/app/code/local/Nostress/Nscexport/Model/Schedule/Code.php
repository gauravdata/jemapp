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

class Nostress_Nscexport_Model_Schedule_Code
{
	protected $data = array();

	/**
	 *
	 * @return array
	 */
	public function get()
	{
		if(count($this->data) == 0) {
			$this->_init();
            natcasesort($this->data);
		}

		return $this->data;
	}

	/**
	 *
	 */
	protected function _init()
	{
		$jobCodes = $this->helperCron()->getAvailableJobCodes();
		foreach($jobCodes->asArray() as $key => $job) {
			$this->_add($key);
		}
	}

	/**
	 *
	 * @param string $data
	 */
	protected function _add($data)
	{
		$this->data[$data] = $this->helperCron()->__($data);
	}

	/**
	 *
	 * @return Varien_Data_Form_Element_Select 
	 */
	public function toFormElementSelect()
	{
		$data = Nostress_Nscexport_Helper_Data::getExportProcessesCodes();
        natcasesort($data);
		array_unshift($data, '');
		$selectType = new Varien_Data_Form_Element_Select();
		$selectType->setName('job_code')
				->setId('job_code')
				->setForm(new Varien_Data_Form())
				->addClass('required-entry')
				->setValues($data);
		return $selectType;
	}

	/**
	 *
	 * @return Nostress_Nscexport_Helper_Data
	 */
	protected function helperCron()
	{
		return Mage::helper('nscexport');
	}

}