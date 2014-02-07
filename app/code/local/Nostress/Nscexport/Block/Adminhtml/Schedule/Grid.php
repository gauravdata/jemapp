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

class Nostress_Nscexport_Block_Adminhtml_Schedule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('cron_schedule_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('scheduled_at');
		$this->setDefaultDir('ASC');
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
		$collection->addFieldToFilter('finished_at', '0000-00-00 00:00:00');        
        $collection->addFieldToFilter('status', array('pending', 'running', 'missed'));
		$collection->addFieldToFilter('job_code', array('like'=>'%nscexport_%'));
        
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
			'header' => $this->helperCron()->__('Id'),
			'width' => '5px',
			'type' => 'text',
			'index' => 'schedule_id',
			'filter' => false,
		));

		$this->addColumn('job_code', array(
			'header' => $this->helperCron()->__('Code'),
			'width' => '200px',
			'type' => 'options',
			'index' => 'job_code',
			'options' => Nostress_Nscexport_Helper_Data::getExportProcessesCodes(),
		));

		$this->addColumn('status', array(
			'header' => $this->helperCron()->__('Status'),
			'width' => '5px',
			'type' => 'options',
			'index' => 'status',
			'options' => Mage::getSingleton('nscexport/schedule_status')->getStatesSchedule(),
		));

		$this->addColumn('starting_in', array(
			'header' => $this->helperCron()->__('Starting In'),
			'index' => 'starting_in',
			'sortable' => false,
			'filter' => false,
			'type' => 'text',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_starting',
		));

		$this->addColumn('scheduled_at', array(
			'header' => $this->helperCron()->__('Scheduled'),
			'index' => 'scheduled_at',
			'type' => 'datetime',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('executed_at', array(
			'header' => $this->helperCron()->__('Executed'),
			'index' => 'executed_at',
			'type' => 'datetime',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('finished_at', array(
			'header' => $this->helperCron()->__('Finished'),
			'index' => 'finished_at',
			'type' => 'datetime',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

        $this->addColumn('runtime', array(
            'header' => $this->helperCron()->__('Runtime'),
            'index' => 'runtime',
            'sortable' => false,
            'filter' => false,
            'type' => 'text',
            'width' => '60px',
            'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_runtime',
        ));

		$this->addColumn('created_at', array(
			'header' => $this->helperCron()->__('Created'),
			'index' => 'created_at',
			'type' => 'datetime',
			'width' => '80px',
			'renderer' => 'nscexport/adminhtml_widget_grid_column_renderer_datetime',
		));

		$this->addColumn('messages', array(
			'header' => $this->helperCron()->__('Messages'),
			'width' => '50px',
			'type' => 'text',
			'index' => 'messages',
		));

		if(Mage::getSingleton('admin/session')->isAllowed('config/nscexport/actions/edit')) {
			$this->addColumn('action',
					array(
						'header' => $this->helperCron()->__('Action'),
						'width' => '50px',
						'type' => 'action',
						'getter' => 'getId',
						'actions' => array(
							array(
								'caption' => $this->helperCron()->__('Edit'),
								'url' => array('base' => '*/*/edit'),
								'field' => 'schedule_id'
							)
						),
						'filter' => false,
						'sortable' => false,
						'index' => 'stores',
						'is_system' => true,
			));
		}

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
	 *
	 */
	public function getRowUrl($row)
	{
		if(Mage::getSingleton('admin/session')->isAllowed('config/nscexport/actions/edit')) {
			return $this->getUrl('*/*/edit', array('schedule_id' => $row->getId()));
		}
		return false;
	}

	/**
	 *
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}

	/**
	 *
	 * @return nscexport_Helper_Data
	 */
	protected function helperCron()
	{
		return Mage::helper('nscexport');
	}

}
