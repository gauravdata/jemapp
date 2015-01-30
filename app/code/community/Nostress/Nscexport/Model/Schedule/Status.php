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

class Nostress_Nscexport_Model_Schedule_Status
{
	protected $states = array();
	protected $statesShort = array();

	/**
	 * Retrieve all states
	 *
	 * @return array
	 */
	public function getStates()
	{
		if(count($this->states) == 0) {
			$this->_initStates();
		}

		return $this->states;
	}

    /**
	 * Retrieve all states for the history grid
	 *
	 * @return array
	 */
	public function getStatesHistory()
	{
        $states_history = array();
        // Translate State
		$states_history[Mage_Cron_Model_Schedule::STATUS_ERROR] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_ERROR);
        $states_history[Mage_Cron_Model_Schedule::STATUS_MISSED] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_MISSED);
        $states_history[Mage_Cron_Model_Schedule::STATUS_SUCCESS] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_SUCCESS);

		return $states_history;
	}

/**
	 * Retrieve all states for the schedule grid
	 *
	 * @return array
	 */
	public function getStatesSchedule()
	{
        $states_schedule = array();
        // Translate State
		$states_schedule[Mage_Cron_Model_Schedule::STATUS_RUNNING] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_RUNNING);
        $states_schedule[Mage_Cron_Model_Schedule::STATUS_MISSED] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_MISSED);
        $states_schedule[Mage_Cron_Model_Schedule::STATUS_PENDING] = Mage::helper('nscexport')->__(Mage_Cron_Model_Schedule::STATUS_PENDING);

		return $states_schedule;
	}


	/**
	 * Retrieve all states with short Text
	 *
	 * @return array
	 */
	public function getStatesShort()
	{
		if(count($this->statesShort) == 0) {
			$this->_initStates();
		}

		return $this->statesShort;
	}

	/**
	 *
	 */
	protected function _initStates()
	{
		$this->_addState(Mage_Cron_Model_Schedule::STATUS_ERROR);
		$this->_addState(Mage_Cron_Model_Schedule::STATUS_MISSED);
		$this->_addState(Mage_Cron_Model_Schedule::STATUS_PENDING);
		$this->_addState(Mage_Cron_Model_Schedule::STATUS_RUNNING);
		$this->_addState(Mage_Cron_Model_Schedule::STATUS_SUCCESS);
	}

	/**
	 *
	 * @param string $state
	 */
	protected function _addState($state)
	{
		// Translate State
		$this->states[$state] = Mage::helper('nscexport')->__($state);

		// Translate Short State Identifier
		$this->statesShort[$state] = Mage::helper('nscexport')->__('short_' . $state);
	}

	/**
	 *
	 * @return Varien_Data_Form_Element_Select 
	 */
	public function toFormElementSelect()
	{
		$data = $this->getStates();
		array_unshift($data, '');
		$selectType = new Varien_Data_Form_Element_Select();
        $selectType->setName('status')
            ->setId('status')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($data);
		return $selectType;
	}

}