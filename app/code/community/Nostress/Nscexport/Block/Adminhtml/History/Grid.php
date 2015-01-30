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
class Nostress_Nscexport_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected  $_helper;
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('cron_history_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('scheduled_at');
		$this->setDefaultDir('ASC');
        $this->setRowClickCallback();
		$this->setSaveParametersInSession(true);
	}

	/**
	 *
	 */
	protected function _prepareCollection()
	{
		/**
		 * @var Mage_Cron_Model_Mysql4_Schedule_Collection $collection
		 */
		$collection = Mage::getModel('cron/schedule')->getCollection();

		// Filter Finished Schedules
        $collection->addFieldToFilter('status', array('success', 'error', 'missed'));
        $collection->addFieldToFilter('job_code', Nostress_Nscexport_Model_Schedule_Code::CRONTAB_JOB_CODE);

		// Order Collection
		$collection->addOrder($this->getOrderSort(), $this->getOrderDir());

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('schedule_id', array(
			'header' => $this->myhelper()->__('Id'),
			'width' => '5px',
			'type' => 'text',
			'index' => 'schedule_id',
			'filter' => false,
		));


		$this->addColumn('job_code', array(
			'header' => $this->myhelper()->__('Code'),
			'width' => '200px',
			'type' => 'options',
			'index' => 'job_code',
			'options' => Mage::getSingleton('nscexport/schedule_code')->getProcessesCodes(),
		));

       $this->addColumn('status', array(
			'header' => $this->myhelper()->__('Status'),
			'width' => '5px',
			'type' => 'options',
			'index' => 'status',
			'options' => Mage::getSingleton('nscexport/schedule_status')->getStatesHistory(),
		));

		$this->addColumn('starting_in', array(
			'header' => $this->myhelper()->__('Starting In'),
			'index' => 'starting_in',
			'sortable' => false,
			'filter' => false,
			'type' => 'text',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_starting',
		));

		$this->addColumn('scheduled_at', array(
			'header' => $this->myhelper()->__('Scheduled'),
			'index' => 'scheduled_at',
			'type' => 'datetime',
			'width' => '100px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('executed_at', array(
			'header' => $this->myhelper()->__('Executed'),
			'index' => 'executed_at',
			'type' => 'text',
			'width' => '100px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('finished_at', array(
			'header' => $this->myhelper()->__('Finished'),
			'index' => 'finished_at',
			'type' => 'text',
			'width' => '100px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

        $this->addColumn('runtime', array(
            'header' => $this->myhelper()->__('Runtime'),
            'index' => 'runtime',
            'sortable' => false,
            'filter' => false,
            'type' => 'text',
            'width' => '60px',
            'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_runtime',
        ));

		$this->addColumn('created_at', array(
			'header' => $this->myhelper()->__('Created'),
			'index' => 'created_at',
			'type' => 'datetime',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('messages', array(
			'header' => $this->myhelper()->__('Messages'),
			'width' => '5px',
			'type' => 'text',
			'index' => 'messages',
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('cron_ids');
		$this->getMassactionBlock()->setUseSelectAll(false);

		$this->getMassactionBlock()->addItem('delete_cron', array(
			'label' => Mage::helper('sales')->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
		));


		return $this;
	}

	/**
	 *
	 */
	protected function getOrderSort()
	{
		return $this->getRequest()->getParam($this->getVarNameSort(), $this->_defaultSort);
	}

	/**
	 *
	 */
	protected function getOrderDir()
	{
		return $this->getRequest()->getParam($this->getVarNameDir(), $this->_defaultDir);
	}

	/**
	 * Url for clicking a row
     * (empty, because the history grid does not allow editing)
	 */
	public function getRowUrl($row)
	{
		return false;
	}

	/**
	 *
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
	
	public function myhelper()
	{
		if(!isset($this->_helper))
			$this->_helper = Mage::helper('nscexport');
		return $this->_helper;
	}

}
