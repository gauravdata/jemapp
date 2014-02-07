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
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('nscexportGrid');
      $this->setDefaultSort('export_id');
      //$this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('nscexport/nscexport')->getCollection();
      foreach($collection as $profile)
      	$profile->setSearchengine(strtolower($profile->getSearchengine()));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	
    $this->addColumn('export_id', array(
    	'header'    => Mage::helper('nscexport')->__('ID'),
      	'align'     =>'right',
      	'width'     => '50px',
      	'index'     => 'export_id',
    ));
           
    $this->addColumn('name', array(
      'header'    => Mage::helper('nscexport')->__('Profile Name'),
      'index'     => 'name',
    ));         
    
    $this->addColumn('enabled', array(
      'header'    => Mage::helper('nscexport')->__('Active'),
      'type'      => 'options',
      'align'     => 'left',
      'width'     => '80px',
      'index'     => 'enabled',      
      'width'     => '50px',
      'options'   => array(
              1 => 'Yes',
              0 => 'No',
      ),
    ));   
    
    $searchEnginesArray = $this->getExportEnginesArray();
    
    $this->addColumn('searchengine', array(
      'header'    => Mage::helper('nscexport')->__('Type'),
      'type'      => 'options',
      'align'     => 'left',
      'index'     => 'searchengine',      
      'options'   => $searchEnginesArray,
    )); 
    
    $this->addColumn('link', array(
            'header'    => Mage::helper('nscexport')->__('XML Url'),
            'index'     => 'url',
            'renderer'  => 'nscexport/adminhtml_nscexport_grid_renderer_link',
        ));

    //get store view name option collection
    $storeOptionsArray = array();
    $storeCollection = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
    foreach ($storeCollection as $store) 
    {
    	$storeOptionsArray[$store->getId()] = $store->getName();
    }
    if(!isset($storeOptionsArray[0]) || $storeOptionsArray[0] == '')
    	$storeOptionsArray[0] = Mage::helper('nscexport')->__('Default (Admin) Values');    
    $this->addColumn('store_title', array(
            'header'        => Mage::helper('core')->__('Store View Name'),
            'type'     		=> 'options',
            'align'         => 'left',
            'index'         => 'store_id',
            'options'   	=> $storeOptionsArray,
    ));

    $this->addColumn('message', array(
		'header'    => Mage::helper('nscexport')->__('Message'),
		'align'     => 'left',
		'type'      => 'longtext',
		'default'   => '--',
		'index'     => 'message',
	));
    
	$this->addColumn('created_time', array(
		'header'    => Mage::helper('nscexport')->__('Creation Time'),
		'align'     => 'left',
		'width'     => '120px',
		'type'      => 'datetime',
		'default'   => '--',
		'index'     => 'created_time',
	));

	$this->addColumn('update_time', array(
		'header'    => Mage::helper('nscexport')->__('Last Time Generated'),
		'align'     => 'left',
		'width'     => '120px',
		'type'      => 'datetime',
		'default'   => '--',
		'index'     => 'update_time',
	));
	

	$this->addColumn('action', array(
            'header'   => Mage::helper('nscexport')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'nscexport/adminhtml_nscexport_grid_renderer_action'
        ));
    
    
    return parent::_prepareColumns();
  }        

  	private function getExportEnginesArray()
  	{
  		$engines = Nostress_Nscexport_Helper_Data::getEngineCollection();
  		$result = array();
  		foreach($engines as $code => $engine)
  		{
  			$result[$code] =  $engine['title'];		
  		}	
  		return $result;
  	}
	/**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('profile');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('generate', array(
             'label'=> Mage::helper('nscexport')->__('Generate'),
             'url'  => $this->getUrl('*/*/massGenerate')
        ));
//
//        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('catalog')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('catalog')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
//
//        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
//            $this->getMassactionBlock()->addItem('attributes', array(
//                'label' => Mage::helper('catalog')->__('Update attributes'),
//                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
//            ));
//        }

        return $this;
    }

}
