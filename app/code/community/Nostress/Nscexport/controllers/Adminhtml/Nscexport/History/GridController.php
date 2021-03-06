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
* Kontroler pro historii exportnich procesu spoustenych pres cron
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Adminhtml_Nscexport_History_GridController extends Mage_Adminhtml_Controller_Action
{

	/**
	 *
	 * @return Mage_Cron_Model_Schedule
	 */
	protected function _initSchedule()
	{
		$scheduleId = (int) $this->getRequest()->getParam('schedule_id');

		$schedule = Mage::getModel('cron/schedule');

		if($scheduleId) {
			$schedule->load($scheduleId);
		}

		Mage::register('nscexport_cron_schedule', $schedule);

		return $schedule;
	}

	/**
	 * Init layout, menu and breadcrumb
	 *
	 * @return Nostress_Cron_Adminhtml_HistoryController
	 */
	protected function _initAction()
	{
		$this->loadLayout()
				->_setActiveMenu('koongoconnector/nscexportcron/history')
				->_addBreadcrumb($this->__('System'), $this->__('System'))
				->_addBreadcrumb($this->__('Schedule'), $this->__('Schedule'));
		return $this;
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('nscexport/adminhtml_history'));
		$this->renderLayout();
	}

	/**
	 * Grid
	 */
	public function gridAction()
	{
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('nscexport/adminhtml_history_grid')->toHtml()
		);
	}
	
	/**
	 * Cancel selected orders
	 */
	public function massDeleteAction()
	{
		$cronIds = $this->getRequest()->getPost('cron_ids', array());
		$countDeleteCron = 0;
		$countNonDeleteCron = 0;
		foreach($cronIds as $cronId)
		{
			try {
				$cron = Mage::getModel('cron/schedule')->load($cronId);
				$cron->delete();
				$countDeleteCron++;
			}
			catch(Exception $e) {
				$countNonDeleteCron++;
			}
		}
		if($countNonDeleteCron) {
			if($countDeleteCron) {
				$this->_getSession()->addError($this->__('%s Cronjob(s) cannot be deleted', $countNonDeleteCron));
			}
			else {
				$this->_getSession()->addError($this->__('The Cronjob(s) cannot be deleted'));
			}
		}
		if($countDeleteCron) {
			$this->_getSession()->addSuccess($this->__('%s Cronjob(s) have been deleted.', $countDeleteCron));
		}
		$this->_redirect('*/*/');
	}
}