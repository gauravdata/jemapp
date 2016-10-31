<?php

class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('exactonlineGrid');
		// This is the primary key of the database
		$this->setDefaultSort('setting_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$categoryId = (int)$this->getRequest()->getParam('category',1);


		$collection = Mage::getResourceModel('exactonline/setting_collection')->distinct(true);

	    $collection->addFieldToFilter("visible","1");
	    $collection->addFieldToFilter('main_table.category_id',$categoryId);

        foreach ($collection as $setting) {
            $setting->setLabel(Mage::helper('exactonline')->__($setting->getLabel()));
        }

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('setting_id', array(
			'header' => Mage::helper('exactonline')->__('ID'),
			'align' =>'right',
			'width' => '10%',
			'index' => 'setting_id',
		));

		$this->addColumn('label', array(
			'header' => Mage::helper('exactonline')->__('Name'),
			'align' =>'left',
			'width' => '20%',
			'index' => 'label',
		));

		$this->addColumn('name', array(
			'header' => Mage::helper('exactonline')->__('Name internal'),
			'align' =>'left',
			'width' => '30%',
			'index' => 'name',
		));

		$this->addColumn('value', array(
			'header' => Mage::helper('exactonline')->__('Value'),
			'align' => 'left',
			'width' => '30%',
			'index' => 'value',
		));

		$this->addColumn('timestamp', array(
			'header' => Mage::helper('exactonline')->__('Creation Time'),
			'align' => 'left',
			'width' => '10%',
			'type' => 'date',
			'default' => '--',
			'index' => 'timestamp',
		));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}